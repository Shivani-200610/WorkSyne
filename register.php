<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize error messages
$emp_error = "";
$empr_error = "";

// Form processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = $_POST['form_type'];
    
    // Function to sanitize input
    function sanitize($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    if ($form_type == 'employee') {
        $name = sanitize($_POST['emp-name']);
        $email = sanitize($_POST['emp-email']);
        $mobile = sanitize($_POST['emp-mobile']);
        $dob = sanitize($_POST['emp-dob']);
        $skill = sanitize($_POST['emp-skill']);
        $password = $_POST['emp-password'];
        $confirm_password = $_POST['emp-confirm-password'];

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $conn->prepare("INSERT INTO Employee (full_name, email, mobile, dob, skillset, password) 
                                      VALUES (:name, :email, :mobile, :dob, :skill, :password)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->bindParam(':dob', $dob);
                $stmt->bindParam(':skill', $skill);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->execute();
                header("Location: employeeDashboard.html");
                exit();
            } catch(PDOException $e) {
                $emp_error = "Error: " . $e->getMessage();
            }
        } else {
            $emp_error = "Employee passwords do not match!";
        }
    } elseif ($form_type == 'employer') {
        $name = sanitize($_POST['empr-name']);
        $email = sanitize($_POST['empr-email']);
        $mobile = sanitize($_POST['empr-mobile']);
        $dob = sanitize($_POST['empr-dob']);
        $company = sanitize($_POST['empr-company']);
        $password = $_POST['empr-password'];
        $confirm_password = $_POST['empr-confirm-password'];

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $conn->prepare("INSERT INTO Employer (full_name, email, mobile, dob, company_name, password) 
                                      VALUES (:name, :email, :mobile, :dob, :company, :password)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mobile', $mobile);
                $stmt->bindParam(':dob', $dob);
                $stmt->bindParam(':company', $company);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->execute();
                header("Location: employerDashboard.html");
                exit();
            } catch(PDOException $e) {
                $empr_error = "Error: " . $e->getMessage();
            }
        } else {
            $empr_error = "Employer passwords do not match!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - WorkSyne</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: #f0f2f5;
      min-height: 100vh;
    }

    .navbar {
      background: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .logo {
      font-family: 'Pacifico', cursive;
      font-size: 24px;
      color: #2A7DE1;
    }

    .tagline {
      color: #666;
      font-size: 14px;
    }

    .icons {
      display: flex;
      gap: 20px;
    }

    .icons i {
      color: #2A7DE1;
      font-size: 20px;
      cursor: pointer;
    }

    .register-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: calc(100vh - 80px);
      padding: 2rem;
    }

    .register-box {
      background: white;
      border-radius: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 2.5rem;
      width: 100%;
      max-width: 500px;
    }

    .register-box h2 {
      color: #2A7DE1;
      margin-bottom: 1.5rem;
      text-align: center;
    }

    .register-type {
      display: flex;
      gap: 10px;
      margin-bottom: 1.5rem;
    }

    .register-type button {
      flex: 1;
      padding: 12px;
      border: 2px solid #2A7DE1;
      background: transparent;
      color: #2A7DE1;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .register-type button.active {
      background: #2A7DE1;
      color: white;
    }

    .form-section {
      display: none;
    }

    .form-group {
      margin-bottom: 1rem;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      font-size: 14px;
    }

    .form-group input {
      width: 100%;
      padding: 12px 12px 12px 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
    }

    .toggle-password {
      position: absolute;
      top: 35px;
      right: 10px;
      cursor: pointer;
      background: none;
      border: none;
      color: #2A7DE1;
      font-size: 14px;
    }

    .submit-btn {
      background: #2A7DE1;
      color: white;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 15px;
      margin-top: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .submit-btn:hover {
      background: #1A5BA3;
    }

    .login-link {
      margin-top: 1.5rem;
      display: block;
      color: #2A7DE1;
      text-align: center;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
    }

    .login-link:hover {
      text-decoration: underline;
    }

    .error-message {
      color: red;
      font-size: 14px;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
  <header class="navbar">
    <div class="logo">WorkSyne</div>
    <div class="tagline">Where Work and Synergy Meet</div>
    <div class="icons">
      <i class="fas fa-bell"></i>
      <i class="fas fa-user-circle"></i>
    </div>
  </header>

  <div class="register-container">
    <div class="register-box">
      <h2>Create Your Account</h2>

      <div class="register-type">
        <button id="btnEmployee" class="active">Employee</button>
        <button id="btnEmployer">Employer</button>
      </div>

      <!-- Employee Form -->
      <form id="formEmployee" class="form-section" method="POST" onsubmit="return validateEmployeeForm()">
        <input type="hidden" name="form_type" value="employee">
        <?php if (!empty($emp_error)): ?>
          <div class="error-message"><?php echo $emp_error; ?></div>
        <?php endif; ?>
        <div class="form-group">
          <label for="emp-name">Full Name</label>
          <input type="text" id="emp-name" name="emp-name" required />
        </div>
        <div class="form-group">
          <label for="emp-email">Email</label>
          <input type="email" id="emp-email" name="emp-email" required />
        </div>
        <div class="form-group">
          <label for="emp-mobile">Mobile Number</label>
          <input type="tel" id="emp-mobile" name="emp-mobile" required />
        </div>
        <div class="form-group">
          <label for="emp-dob">Date of Birth</label>
          <input type="date" id="emp-dob" name="emp-dob" min="1960-01-01" max="2025-12-31" required />
        </div>
        <div class="form-group">
          <label for="emp-skill">Skillset</label>
          <input type="text" id="emp-skill" name="emp-skill" placeholder="E.g., Electrician, Maid" required />
        </div>
        <div class="form-group">
          <label for="emp-password">Password</label>
          <input type="password" id="emp-password" name="emp-password" required />
          <button type="button" class="toggle-password" onclick="togglePassword('emp-password', this)">Show</button>
        </div>
        <div class="form-group">
          <label for="emp-confirm-password">Confirm Password</label>
          <input type="password" id="emp-confirm-password" name="emp-confirm-password" required />
          <button type="button" class="toggle-password" onclick="togglePassword('emp-confirm-password', this)">Show</button>
        </div>
        <button type="submit" class="submit-btn">Register</button>
        <a href="login.php" class="login-link">Already have an account? Login</a>
      </form>

      <!-- Employer Form -->
      <form id="formEmployer" class="form-section" method="POST" onsubmit="return validateEmployerForm()">
        <input type="hidden" name="form_type" value="employer">
        <?php if (!empty($empr_error)): ?>
          <div class="error-message"><?php echo $empr_error; ?></div>
        <?php endif; ?>
        <div class="form-group">
          <label for="empr-name">Full Name</label>
          <input type="text" id="empr-name" name="empr-name" required />
        </div>
        <div class="form-group">
          <label for="empr-email">Email</label>
          <input type="email" id="empr-email" name="empr-email" required />
        </div>
        <div class="form-group">
          <label for="empr-mobile">Mobile Number</label>
          <input type="tel" id="empr-mobile" name="empr-mobile" required />
        </div>
        <div class="form-group">
          <label for="empr-dob">Date of Birth</label>
          <input type="date" id="empr-dob" name="empr-dob" min="1960-01-01" max="2025-12-31" required />
        </div>
        <div class="form-group">
          <label for="empr-company">Company Name</label>
          <input type="text" id="empr-company" name="empr-company" required />
        </div>
        <div class="form-group">
          <label for="empr-password">Password</label>
          <input type="password" id="empr-password" name="empr-password" required />
          <button type="button" class="toggle-password" onclick="togglePassword('empr-password', this)">Show</button>
        </div>
        <div class="form-group">
          <label for="empr-confirm-password">Confirm Password</label>
          <input type="password" id="empr-confirm-password" name="empr-confirm-password" required />
          <button type="button" class="toggle-password" onclick="togglePassword('empr-confirm-password', this)">Show</button>
        </div>
        <button type="submit" class="submit-btn">Register</button>
        <a href="login.html" class="login-link">Already have an account? Login</a>
      </form>
    </div>
  </div>

  <script>
    const btnEmployee = document.getElementById('btnEmployee');
    const btnEmployer = document.getElementById('btnEmployer');
    const formEmployee = document.getElementById('formEmployee');
    const formEmployer = document.getElementById('formEmployer');

    function showForm(type) {
      if (type === 'employee') {
        formEmployee.style.display = 'block';
        formEmployer.style.display = 'none';
        btnEmployee.classList.add('active');
        btnEmployer.classList.remove('active');
      } else {
        formEmployer.style.display = 'block';
        formEmployee.style.display = 'none';
        btnEmployer.classList.add('active');
        btnEmployee.classList.remove('active');
      }
    }

    btnEmployee.addEventListener('click', () => showForm('employee'));
    btnEmployer.addEventListener('click', () => showForm('employer'));

    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Hide';
      } else {
        input.type = 'password';
        btn.textContent = 'Show';
      }
    }

    // Show employee form by default
    showForm('employee');

    function validateEmployeeForm() {
      const pass = document.getElementById('emp-password').value;
      const confirm = document.getElementById('emp-confirm-password').value;
      if (pass !== confirm) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = 'Employee passwords do not match!';
        const form = document.querySelector('#formEmployee');
        const existingError = form.querySelector('.error-message');
        if (existingError) existingError.remove();
        form.insertBefore(errorDiv, form.firstChild);
        return false;
      }
      return true;
    }

    function validateEmployerForm() {
      const pass = document.getElementById('empr-password').value;
      const confirm = document.getElementById('empr-confirm-password').value;
      if (pass !== confirm) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = 'Employer passwords do not match!';
        const form = document.querySelector('#formEmployer');
        const existingError = form.querySelector('.error-message');
        if (existingError) existingError.remove();
        form.insertBefore(errorDiv, form.firstChild);
        return false;
      }
      return true;
    }
  </script>
</body>
</html>