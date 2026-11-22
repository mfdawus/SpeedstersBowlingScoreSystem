<?php

require_once __DIR__ . '/../database.php';

function hdcpValidateDate($date) {
    $parsed = DateTime::createFromFormat('!Y-m-d', (string)$date);
    return $parsed && $parsed->format('Y-m-d') === $date;
}

function hdcpIsNonNegativeInteger($value) {
    return is_scalar($value) && preg_match('/^\d+$/', (string)$value) === 1;
}

function hdcpPinsForTotal($total, $threshold, $freePinsPerGame) {
    return (int)$total < (int)$threshold ? (int)$freePinsPerGame : 0;
}

function hdcpGetSession(PDO $pdo, $sessionId) {
    $stmt = $pdo->prepare("
        SELECT session_id, game_mode, hdcp_evaluation_start, hdcp_evaluation_end,
               hdcp_threshold_total, hdcp_free_pins_per_game, hdcp_status
        FROM game_sessions
        WHERE session_id = ?
    ");
    $stmt->execute([$sessionId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function hdcpSaveConfiguration($sessionId, $evaluationStart, $evaluationEnd, $threshold, $pinsPerGame) {
    if (!hdcpValidateDate($evaluationStart) || !hdcpValidateDate($evaluationEnd) || $evaluationStart > $evaluationEnd) {
        throw new InvalidArgumentException('Choose a valid evaluation date range.');
    }
    if (!hdcpIsNonNegativeInteger($threshold) || !hdcpIsNonNegativeInteger($pinsPerGame)) {
        throw new InvalidArgumentException('Threshold and free pins must be non-negative whole numbers.');
    }

    $pdo = getDBConnection();
    $pdo->beginTransaction();
    try {
        $session = hdcpGetSession($pdo, $sessionId);
        if (!$session) {
            throw new InvalidArgumentException('Session not found.');
        }
        if ($session['hdcp_status'] === 'Locked') {
            throw new InvalidArgumentException('Locked HDCP settings cannot be changed.');
        }

        $stmt = $pdo->prepare("
            DELETE FROM session_handicaps
            WHERE session_id = ? AND decision IN ('Pending', 'Approved')
        ");
        $stmt->execute([$sessionId]);
        $stmt = $pdo->prepare("
            UPDATE game_sessions
            SET hdcp_evaluation_start = ?, hdcp_evaluation_end = ?,
                hdcp_threshold_total = ?, hdcp_free_pins_per_game = ?, hdcp_status = 'Draft'
            WHERE session_id = ?
        ");
        $stmt->execute([$evaluationStart, $evaluationEnd, (int)$threshold, (int)$pinsPerGame, $sessionId]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function hdcpGetSessionUnits(PDO $pdo, $sessionId, $gameMode) {
    if ($gameMode === 'Solo') {
        $sql = "
            SELECT CAST(sp.user_id AS CHAR) AS subject_key,
                   CONCAT(u.first_name, ' ', u.last_name) AS label,
                   u.default_hdcp_pins_per_game AS player_default_hdcp_per_game
            FROM session_participants sp
            JOIN users u ON u.user_id = sp.user_id
            WHERE sp.session_id = ?
            UNION
            SELECT CAST(gs.user_id AS CHAR), CONCAT(u.first_name, ' ', u.last_name),
                   u.default_hdcp_pins_per_game
            FROM game_scores gs
            JOIN users u ON u.user_id = gs.user_id
            WHERE gs.session_id = ? AND gs.game_mode = 'Solo'
        ";
    } elseif ($gameMode === 'Doubles') {
        $sql = "
            SELECT CAST(dt.duo_id AS CHAR) AS subject_key, dt.duo_name AS label,
                   NULL AS player_default_hdcp_per_game
            FROM duo_teams dt
            WHERE dt.session_id = ? AND dt.status != 'Cancelled'
            UNION
            SELECT CAST(gs.duo_id AS CHAR), COALESCE(dt.duo_name, CONCAT('Duo #', gs.duo_id)),
                   NULL
            FROM game_scores gs
            LEFT JOIN duo_teams dt ON dt.duo_id = gs.duo_id AND dt.session_id = gs.session_id
            WHERE gs.session_id = ? AND gs.game_mode = 'Doubles' AND gs.duo_id IS NOT NULL
        ";
    } elseif ($gameMode === 'Team') {
        $sql = "
            SELECT DISTINCT u.team_name AS subject_key, u.team_name AS label,
                   NULL AS player_default_hdcp_per_game
            FROM session_participants sp
            JOIN users u ON u.user_id = sp.user_id
            WHERE sp.session_id = ? AND u.team_name IS NOT NULL AND u.team_name != ''
            UNION
            SELECT DISTINCT gs.team_name, gs.team_name, NULL
            FROM game_scores gs
            WHERE gs.session_id = ? AND gs.game_mode = 'Team'
              AND gs.team_name IS NOT NULL AND gs.team_name != ''
        ";
    } else {
        throw new InvalidArgumentException('Unsupported game mode for HDCP.');
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sessionId, $sessionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hdcpGetEvaluationTotal(PDO $pdo, $sessionId, $subjectType, $subjectKey, $gameMode, $dateStart, $dateEnd) {
    if ($subjectType === 'Player') {
        $sql = "SELECT COALESCE(SUM(COALESCE(scratch_score, player_score)), 0)
                FROM game_scores
                WHERE user_id = ? AND game_mode = ? AND status = 'Completed'
                  AND game_date BETWEEN ? AND ?";
        $key = (int)$subjectKey;
    } elseif ($subjectType === 'Duo') {
        // Duo IDs are session-scoped, so history follows the same pair across sessions.
        $sql = "SELECT COALESCE(SUM(COALESCE(gs.scratch_score, gs.player_score)), 0)
                FROM game_scores gs
                JOIN duo_teams historical_duo
                  ON historical_duo.duo_id = gs.duo_id
                 AND historical_duo.session_id = gs.session_id
                JOIN duo_teams target_duo
                  ON target_duo.session_id = ? AND target_duo.duo_id = ?
                WHERE gs.game_mode = ? AND gs.status = 'Completed'
                  AND gs.game_date BETWEEN ? AND ?
                  AND (
                    (historical_duo.player1_id = target_duo.player1_id AND historical_duo.player2_id = target_duo.player2_id)
                    OR (historical_duo.player1_id = target_duo.player2_id AND historical_duo.player2_id = target_duo.player1_id)
                  )";
        $params = [$sessionId, (int)$subjectKey, $gameMode, $dateStart, $dateEnd];
    } else {
        $sql = "SELECT COALESCE(SUM(COALESCE(scratch_score, player_score)), 0)
                FROM game_scores
                WHERE team_name = ? AND game_mode = ? AND status = 'Completed'
                  AND game_date BETWEEN ? AND ?";
        $key = $subjectKey;
    }

    $stmt = $pdo->prepare($sql);
    if ($subjectType === 'Duo') {
        $stmt->execute($params);
    } else {
        $stmt->execute([$key, $gameMode, $dateStart, $dateEnd]);
    }
    return (int)$stmt->fetchColumn();
}

function hdcpEvaluateSession($sessionId) {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    try {
        $session = hdcpGetSession($pdo, $sessionId);
        if (!$session || !$session['hdcp_evaluation_start'] || !$session['hdcp_evaluation_end']
            || $session['hdcp_threshold_total'] === null) {
            throw new InvalidArgumentException('Save the evaluation period, threshold, and free pins first.');
        }
        if ($session['hdcp_status'] === 'Locked') {
            throw new InvalidArgumentException('Locked HDCP awards cannot be re-evaluated.');
        }

        $stmt = $pdo->prepare("
            DELETE FROM session_handicaps
            WHERE session_id = ? AND decision IN ('Pending', 'Approved')
        ");
        $stmt->execute([$sessionId]);

        $subjectType = $session['game_mode'] === 'Solo' ? 'Player'
            : ($session['game_mode'] === 'Doubles' ? 'Duo' : 'Team');
        $units = hdcpGetSessionUnits($pdo, $sessionId, $session['game_mode']);
        $insert = $pdo->prepare("
            INSERT INTO session_handicaps
                (session_id, subject_type, subject_key, evaluation_start, evaluation_end,
                 evaluation_total, threshold_total, hdcp_pins_per_game)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $awards = [];
        foreach ($units as $unit) {
            $total = hdcpGetEvaluationTotal(
                $pdo,
                $sessionId,
                $subjectType,
                $unit['subject_key'],
                $session['game_mode'],
                $session['hdcp_evaluation_start'],
                $session['hdcp_evaluation_end']
            );
            $calculatedPins = hdcpPinsForTotal(
                $total,
                $session['hdcp_threshold_total'],
                $session['hdcp_free_pins_per_game']
            );
            $playerDefaultPins = $subjectType === 'Player'
                ? (int)$unit['player_default_hdcp_per_game']
                : 0;
            $pins = $playerDefaultPins > 0 ? $playerDefaultPins : $calculatedPins;
            $insert->execute([
                $sessionId, $subjectType, $unit['subject_key'],
                $session['hdcp_evaluation_start'], $session['hdcp_evaluation_end'],
                $total, $session['hdcp_threshold_total'], $pins
            ]);
            $awards[] = [
                'handicap_id' => (int)$pdo->lastInsertId(),
                'subject_type' => $subjectType,
                'subject_key' => $unit['subject_key'],
                'label' => $unit['label'],
                'evaluation_total' => $total,
                'threshold_total' => (int)$session['hdcp_threshold_total'],
                'player_default_hdcp_per_game' => $subjectType === 'Player' ? $playerDefaultPins : null,
                'hdcp_pins_per_game' => $pins,
                'decision' => 'Pending',
                'is_override' => 0
            ];
        }

        $stmt = $pdo->prepare("UPDATE game_sessions SET hdcp_status = 'Draft' WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $pdo->commit();
        return $awards;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function hdcpGetAwards($sessionId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT sh.handicap_id, sh.subject_type, sh.subject_key, sh.evaluation_start, sh.evaluation_end,
               sh.evaluation_total, sh.threshold_total, sh.hdcp_pins_per_game, sh.decision,
               sh.is_override, sh.override_reason,
               CASE WHEN sh.subject_type = 'Player' THEN COALESCE(u.default_hdcp_pins_per_game, 0)
                    ELSE NULL END AS player_default_hdcp_per_game,
               CASE sh.subject_type
                   WHEN 'Player' THEN CONCAT(u.first_name, ' ', u.last_name)
                   WHEN 'Duo' THEN COALESCE(dt.duo_name, CONCAT('Duo #', sh.subject_key))
                   ELSE sh.subject_key
               END AS label
        FROM session_handicaps sh
        LEFT JOIN users u ON sh.subject_type = 'Player' AND u.user_id = CAST(sh.subject_key AS UNSIGNED)
        LEFT JOIN duo_teams dt ON sh.subject_type = 'Duo' AND dt.duo_id = CAST(sh.subject_key AS UNSIGNED)
            AND dt.session_id = sh.session_id
        WHERE sh.session_id = ?
        ORDER BY sh.subject_type, sh.subject_key
    ");
    $stmt->execute([$sessionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hdcpApproveAward($awardId, $adminId, $pinsPerGame, $overrideReason = '') {
    if (!hdcpIsNonNegativeInteger($pinsPerGame)) {
        throw new InvalidArgumentException('Free pins must be a non-negative whole number.');
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT sh.*, gs.hdcp_status
        FROM session_handicaps sh
        JOIN game_sessions gs ON gs.session_id = sh.session_id
        WHERE sh.handicap_id = ?
    ");
    $stmt->execute([$awardId]);
    $award = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$award || !in_array($award['decision'], ['Pending', 'Approved'], true) || $award['hdcp_status'] === 'Locked') {
        throw new InvalidArgumentException('This HDCP award cannot be changed after it is locked.');
    }

    $override = (int)$pinsPerGame !== (int)$award['hdcp_pins_per_game'];
    if ($override && trim($overrideReason) === '') {
        throw new InvalidArgumentException('Enter a reason for changing the calculated pins.');
    }
    $stmt = $pdo->prepare("
        UPDATE session_handicaps
        SET hdcp_pins_per_game = ?, is_override = ?, override_reason = ?,
            decision = 'Approved', approved_by = ?, approved_at = NOW()
        WHERE handicap_id = ? AND decision IN ('Pending', 'Approved')
    ");
    $stmt->execute([
        (int)$pinsPerGame, $override ? 1 : 0,
        $override ? trim($overrideReason) : null, $adminId, $awardId
    ]);

    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM session_handicaps
        WHERE session_id = ? AND decision = 'Pending'
    ");
    $stmt->execute([$award['session_id']]);
    if ((int)$stmt->fetchColumn() === 0) {
        $stmt = $pdo->prepare("UPDATE game_sessions SET hdcp_status = 'Approved' WHERE session_id = ?");
        $stmt->execute([$award['session_id']]);
    }
    return true;
}

function hdcpLockSession($sessionId) {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    try {
        $session = hdcpGetSession($pdo, $sessionId);
        if (!$session || $session['hdcp_status'] !== 'Approved') {
            throw new InvalidArgumentException('Approve every award before locking HDCP.');
        }
        $stmt = $pdo->prepare("
            SELECT COUNT(*), SUM(decision = 'Pending')
            FROM session_handicaps
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $counts = $stmt->fetch(PDO::FETCH_NUM);
        if ((int)$counts[0] === 0 || (int)$counts[1] > 0) {
            throw new InvalidArgumentException('Evaluate the session and approve all listed awards before locking.');
        }

        $stmt = $pdo->prepare("
            UPDATE session_handicaps
            SET decision = 'Locked', locked_at = NOW()
            WHERE session_id = ? AND decision = 'Approved'
        ");
        $stmt->execute([$sessionId]);
        $stmt = $pdo->prepare("UPDATE game_sessions SET hdcp_status = 'Locked' WHERE session_id = ?");
        $stmt->execute([$sessionId]);

        $stmt = $pdo->prepare("
            SELECT score_id, game_number, game_mode, user_id, duo_id, team_name
            FROM game_scores
            WHERE session_id = ? AND status = 'Completed'
            ORDER BY score_id
        ");
        $stmt->execute([$sessionId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $score) {
            // Apply awards only to the target session; evaluation-period games stay untouched.
            hdcpApplyToScore(
                $pdo,
                $sessionId,
                $score['score_id'],
                $score['game_number'],
                $score['game_mode'],
                $score['user_id'],
                $score['duo_id'],
                $score['team_name']
            );
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function hdcpReopenSession($sessionId) {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            SELECT hdcp_status
            FROM game_sessions
            WHERE session_id = ?
            FOR UPDATE
        ");
        $stmt->execute([$sessionId]);
        $status = $stmt->fetchColumn();
        if ($status !== 'Locked') {
            throw new InvalidArgumentException('Only a locked HDCP session can be reopened.');
        }

        $stmt = $pdo->prepare("
            UPDATE session_handicaps
            SET decision = 'Pending', approved_by = NULL, approved_at = NULL, locked_at = NULL
            WHERE session_id = ? AND decision = 'Locked'
        ");
        $stmt->execute([$sessionId]);
        $stmt = $pdo->prepare("UPDATE game_sessions SET hdcp_status = 'Draft' WHERE session_id = ?");
        $stmt->execute([$sessionId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function hdcpEnsureScoringAllowed(PDO $pdo, $sessionId) {
    $session = hdcpGetSession($pdo, $sessionId);
    if (!$session) {
        throw new InvalidArgumentException('Session not found.');
    }
    if (!in_array($session['hdcp_status'], ['Disabled', 'Locked'], true)) {
        throw new InvalidArgumentException('Approve and lock this session HDCP before finalizing scores.');
    }
    return $session;
}

function hdcpApplyToScore(PDO $pdo, $sessionId, $scoreId, $gameNumber, $gameMode, $userId, $duoId, $teamName) {
    $session = hdcpEnsureScoringAllowed($pdo, $sessionId);
    if ($session['hdcp_status'] !== 'Locked') {
        $pins = 0;
        if ($gameMode === 'Solo') {
            $stmt = $pdo->prepare("SELECT default_hdcp_pins_per_game FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $pins = (int)$stmt->fetchColumn();
        }
        $stmt = $pdo->prepare("
            UPDATE game_scores
            SET hdcp_applied = ?, hdcp_award_id = NULL
            WHERE score_id = ?
        ");
        $stmt->execute([$pins, $scoreId]);
        return;
    }

    if ($gameMode === 'Solo') {
        $subjectType = 'Player';
        $subjectKey = (string)$userId;
        $scopeSql = 'user_id = ?';
        $scopeKey = (int)$userId;
    } elseif ($gameMode === 'Doubles' && $duoId) {
        $subjectType = 'Duo';
        $subjectKey = (string)$duoId;
        $scopeSql = 'duo_id = ?';
        $scopeKey = (int)$duoId;
    } elseif ($gameMode === 'Team' && $teamName !== null && $teamName !== '') {
        $subjectType = 'Team';
        $subjectKey = (string)$teamName;
        $scopeSql = 'team_name = ?';
        $scopeKey = (string)$teamName;
    } else {
        $stmt = $pdo->prepare("UPDATE game_scores SET hdcp_applied = 0, hdcp_award_id = NULL WHERE score_id = ?");
        $stmt->execute([$scoreId]);
        return;
    }

    // Keep a unit-game bonus on one deterministic row instead of multiplying it by its players.
    $stmt = $pdo->prepare("
        SELECT handicap_id, hdcp_pins_per_game
        FROM session_handicaps
        WHERE session_id = ? AND subject_type = ? AND subject_key = ? AND decision = 'Locked'
    ");
    $stmt->execute([$sessionId, $subjectType, $subjectKey]);
    $award = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gameMode === 'Solo') {
        $stmt = $pdo->prepare("
            UPDATE game_scores
            SET hdcp_applied = ?, hdcp_award_id = ?
            WHERE score_id = ?
        ");
        $stmt->execute([
            $award ? (int)$award['hdcp_pins_per_game'] : 0,
            $award ? (int)$award['handicap_id'] : null,
            $scoreId
        ]);
        return;
    }

    $stmt = $pdo->prepare("
        UPDATE game_scores
        SET hdcp_applied = 0, hdcp_award_id = NULL
        WHERE session_id = ? AND game_number = ? AND game_mode = ? AND $scopeSql
          AND status = 'Completed'
    ");
    $stmt->execute([$sessionId, $gameNumber, $gameMode, $scopeKey]);
    if (!$award) {
        return;
    }

    $stmt = $pdo->prepare("
        SELECT MIN(score_id)
        FROM game_scores
        WHERE session_id = ? AND game_number = ? AND game_mode = ? AND $scopeSql
          AND status = 'Completed'
    ");
    $stmt->execute([$sessionId, $gameNumber, $gameMode, $scopeKey]);
    $anchorScoreId = $stmt->fetchColumn();
    if ($anchorScoreId) {
        $stmt = $pdo->prepare("
            UPDATE game_scores
            SET hdcp_applied = ?, hdcp_award_id = ?
            WHERE score_id = ?
        ");
        $stmt->execute([(int)$award['hdcp_pins_per_game'], (int)$award['handicap_id'], $anchorScoreId]);
    }
}
