<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API Documentation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Admin Panel</a>
    </div>
  </nav>

  <div class="container-fluid mt-4">
    <h2 class="mb-4">API Documentation</h2>

    <div class="accordion" id="apiAccordion">

      <!-- Auth APIs -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="authHeading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#authCollapse" aria-expanded="true" aria-controls="authCollapse">
            🔐 Authentication APIs
          </button>
        </h2>
        <div id="authCollapse" class="accordion-collapse collapse show" aria-labelledby="authHeading" data-bs-parent="#apiAccordion">
          <div class="accordion-body">
            <ul>
              <li>POST /api/register</li>
              <li>POST /api/login</li>
              <li>POST /api/logout</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- User APIs -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="userHeading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#userCollapse" aria-expanded="false" aria-controls="userCollapse">
            👥 User APIs
          </button>
        </h2>
        <div id="userCollapse" class="accordion-collapse collapse" aria-labelledby="userHeading" data-bs-parent="#apiAccordion">
          <div class="accordion-body">
            <ul>
              <li>POST /api/users</li>
              <li>POST /api/users/{id}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Group APIs -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="groupHeading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#groupCollapse" aria-expanded="false" aria-controls="groupCollapse">
            📂 Group APIs
          </button>
        </h2>
        <div id="groupCollapse" class="accordion-collapse collapse" aria-labelledby="groupHeading" data-bs-parent="#apiAccordion">
          <div class="accordion-body">
            <ul>
              <li>GET /api/groups</li>
              <li>POST /api/groups</li>
              <li>GET /api/groups/{id}</li>
              <li>PUT /api/groups/{id}</li>
              <li>DELETE /api/groups/{id}</li>
              <li>GET /api/my-groups</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Expense APIs -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="expenseHeading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#expenseCollapse" aria-expanded="false" aria-controls="expenseCollapse">
            💰 Expense APIs
          </button>
        </h2>
        <div id="expenseCollapse" class="accordion-collapse collapse" aria-labelledby="expenseHeading" data-bs-parent="#apiAccordion">
          <div class="accordion-body">
            <ul>
              <li>GET /api/expenses</li>
              <li>POST /api/expenses</li>
              <li>GET /api/expenses/{id}</li>
              <li>PUT /api/expenses/{id}</li>
              <li>DELETE /api/expenses/{id}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Dashboard API -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="dashboardHeading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardCollapse" aria-expanded="false" aria-controls="dashboardCollapse">
            📊 Dashboard API
          </button>
        </h2>
        <div id="dashboardCollapse" class="accordion-collapse collapse" aria-labelledby="dashboardHeading" data-bs-parent="#apiAccordion">
          <div class="accordion-body">
            <ul>
              <li>GET /api/dashboard/summary</li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
