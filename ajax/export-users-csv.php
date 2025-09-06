<?php
require_once '../includes/auth.php';
require_once '../database.php';

// Require admin access
requireAdmin();

// Set headers for CSV file download (Excel compatible)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

// Add BOM for UTF-8 to ensure proper Excel compatibility
echo "\xEF\xBB\xBF";

try {
    $pdo = getDBConnection();
    
    // Get all users
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            u.skill_level,
            u.user_role,
            u.status,
            u.team_name,
            u.created_at,
            COUNT(gs.score_id) as games_played,
            COALESCE(AVG(gs.player_score), 0) as avg_score,
            COALESCE(MAX(gs.player_score), 0) as best_score
        FROM users u
        LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
        WHERE u.status != 'Deleted'
        GROUP BY u.user_id
        ORDER BY u.first_name, u.last_name
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Function to escape CSV values
    function escapeCSV($value) {
        if (is_numeric($value)) {
            return $value;
        }
        // Escape quotes and wrap in quotes if contains comma, quote, or newline
        if (strpos($value, '"') !== false || strpos($value, ',') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }
    
    // Create CSV content
    $csvContent = '';
    
    // Header row
    $headers = [
        'ID', 'Username', 'First Name', 'Last Name', 'Email', 'Phone', 
        'Skill Level', 'Role', 'Status', 'Team Name', 'Games Played', 
        'Average Score', 'Best Score', 'Joined Date'
    ];
    $csvContent .= implode(',', array_map('escapeCSV', $headers)) . "\n";
    
    // Data rows
    foreach ($users as $user) {
        $row = [
            $user['user_id'],
            $user['username'],
            $user['first_name'],
            $user['last_name'],
            $user['email'],
            $user['phone'] ?: '',
            $user['skill_level'],
            $user['user_role'],
            $user['status'],
            $user['team_name'] ?: 'No Team',
            $user['games_played'],
            $user['games_played'] > 0 ? round($user['avg_score'], 1) : '',
            $user['best_score'] > 0 ? $user['best_score'] : '',
            date('Y-m-d', strtotime($user['created_at']))
        ];
        
        $csvContent .= implode(',', array_map('escapeCSV', $row)) . "\n";
    }
    
    // Output the CSV file
    echo $csvContent;
    
} catch (Exception $e) {
    // If there's an error, output a simple CSV with error message
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="users_export_error_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    echo "ID,Username,First Name,Last Name,Email,Phone,Skill Level,Role,Status,Team Name,Games Played,Average Score,Best Score,Joined Date\n";
    echo "1,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error\n";
    echo "Error Details: " . $e->getMessage() . "\n";
}
?>
