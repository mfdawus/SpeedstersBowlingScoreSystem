<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 text-center">
            <img id="viewUserAvatar" src="assets/images/profile/user-1.jpg" alt="User Avatar" class="rounded-circle mb-3" width="100" height="100">
            <h5 id="viewUserName" class="mb-1"></h5>
            <p id="viewUserTeam" class="text-muted mb-0"></p>
          </div>
          <div class="col-md-8">
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Username:</strong></div>
              <div class="col-sm-8" id="viewUserUsername"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Email:</strong></div>
              <div class="col-sm-8" id="viewUserEmail"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Phone:</strong></div>
              <div class="col-sm-8" id="viewUserPhone"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Skill Level:</strong></div>
              <div class="col-sm-8" id="viewUserSkill"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Status:</strong></div>
              <div class="col-sm-8" id="viewUserStatus"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Games Played:</strong></div>
              <div class="col-sm-8" id="viewUserGames"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Best Score:</strong></div>
              <div class="col-sm-8" id="viewUserBestScore"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Average Score:</strong></div>
              <div class="col-sm-8" id="viewUserAvgScore"></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4"><strong>Joined:</strong></div>
              <div class="col-sm-8" id="viewUserJoined"></div>
            </div>
          </div>
        </div>
        
        <!-- Recent Games -->
        <hr>
        <h6>Recent Games</h6>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Date</th>
                <th>Mode</th>
                <th>Score</th>
                <th>Team</th>
                <th>Lane</th>
              </tr>
            </thead>
            <tbody id="viewUserGamesTable">
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" onclick="editUserFromView()">
          <i class="ti ti-edit me-2"></i>Edit User
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editUserForm">
        <div class="modal-body">
          <input type="hidden" id="editUserId" name="user_id">
          <div class="mb-3">
            <label for="editUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="editUsername" name="username" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editFirstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="editFirstName" name="first_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="editLastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="editLastName" name="last_name">
            </div>
          </div>
          <div class="mb-3">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="editPhone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="editPhone" name="phone">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editSkillLevel" class="form-label">Skill Level</label>
              <select class="form-select" id="editSkillLevel" name="skill_level" required>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="pro">Professional</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="editStatus" class="form-label">Status</label>
              <select class="form-select" id="editStatus" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Suspended">Suspended</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label for="editTeamName" class="form-label">Team Name</label>
            <select class="form-select" id="editTeamName" name="team_name">
              <option value="">No Team</option>
              <option value="Speedsters">Speedsters</option>
              <option value="Crystal Strikes">Crystal Strikes</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createUserForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="createUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="createUsername" name="username" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="createFirstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="createFirstName" name="first_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="createLastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="createLastName" name="last_name">
            </div>
          </div>
          <div class="mb-3">
            <label for="createEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="createEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="createPhone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="createPhone" name="phone">
          </div>
          <div class="mb-3">
            <label for="createPassword" class="form-label">Password</label>
            <input type="password" class="form-control" id="createPassword" name="password" value="password123" required>
            <small class="form-text text-muted">Default password is "password123"</small>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="createSkillLevel" class="form-label">Skill Level</label>
              <select class="form-select" id="createSkillLevel" name="skill_level" required>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="pro">Professional</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="createStatus" class="form-label">Status</label>
              <select class="form-select" id="createStatus" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Suspended">Suspended</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label for="createTeamName" class="form-label">Team Name</label>
            <select class="form-select" id="createTeamName" name="team_name">
              <option value="">No Team</option>
              <option value="Speedsters">Speedsters</option>
              <option value="Crystal Strikes">Crystal Strikes</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>
