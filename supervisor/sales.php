<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Fetch order and inventory data
$ordertotal_values = [];
$orderdate_values = [];
$total_sum = 0;
try {
  $sql = "SELECT * FROM dbhenrichfoodcorps.customerorder";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $ordertotal_values[] = $row["ordertotal"];
      $orderdate_values[] = $row["orderdate"];
      $total_sum += $row["ordertotal"];
    }
  } else {
    echo "No records matching your query were found.";
  }
} catch (Exception $e) {
  die("ERROR: Could not able to execute $sql." . $e->getMessage());
}

// Populate chart data when page is first loaded
$chart_labels_chart = array();
$chart_data_chart = array();
$sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
              FROM customerorder oh
              JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
              GROUP BY pl.productname
              ORDER BY total_sales DESC
              LIMIT 5";
$result_polar = $conn->query($sql_polar);
if ($result_polar->num_rows > 0) {
  while ($row = $result_polar->fetch_assoc()) {
    $chart_labels_chart[] = $row["productname"];
    $chart_data_chart[] = $row["total_sales"];
  }
}
// SQL query for polar area chart
if (isset($_GET['updateChartData'])) {
  $filtered_year = $_GET['year'];
  $filtered_month = $_GET['month'];
  $filtered_day = $_GET['day'];
  $sql_polar = "SELECT pl.productname, SUM(oh.ordertotal) AS total_sales
                FROM customerorder oh
                JOIN productlist pl ON oh.orderdescription LIKE CONCAT('%', pl.productname, '%')
                WHERE YEAR(oh.orderdate) = '$filtered_year' AND MONTH(oh.orderdate) = '$filtered_month' AND DAY(oh.orderdate) = '$filtered_day'
                GROUP BY pl.productname
                ORDER BY total_sales DESC
                LIMIT 10";
  $result_polar = $conn->query($sql_polar);
  $chart_labels_chart = array();
  $chart_data_chart = array();
  if ($result_polar->num_rows > 0) {
    while ($row = $result_polar->fetch_assoc()) {
      $chart_labels_chart[] = $row["productname"];
      $chart_data_chart[] = $row["total_sales"];
    }
  }
  $data = array(
    'labels' => $chart_labels_chart,
    'data' => $chart_data_chart
  );
  echo json_encode($data);
  exit;
}
// Get unique years, months, and days from orderdate
$years = array_unique(array_map(fn($date) => date('Y', strtotime($date)), $orderdate_values));
$months = array_unique(array_map(fn($date) => date('m', strtotime($date)), $orderdate_values));
$days = array_unique(array_map(fn($date) => date('d', strtotime($date)), $orderdate_values));



// Fetch data for recommendations table
$query = "SELECT * FROM customerorder";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
  $label = $row['orderdescription'];
  // retrieve other data here
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>HOME</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  <style type="text/css">
    .chartscontainer {
      display: flex;
      flex-direction: row;
      gap: 20px;
      width: 100%;
      height: 100%;
      align-items: center;
      justify-content: center;
    }

    .chartscontainer>div {
      width: 50%;
    }

    .chartBox,
    .polarAreaChart {
      /* width: 48%; */
      float: left;
      padding: 0.5rem;
      height: 100%;
      width: 100%;
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
      margin-bottom: 1rem;
    }

    .chartBox canvas,
    .polarAreaChart canvas {
      background-color: #e9ecef;
      height: 100%;
      width: 100%;
      border-radius: 5px;
    }

    /* @media (max-width: 600px) {

      .chartBox,
      .polarAreaChart {
        width: 100%;
        margin: 0.5rem 0;
      }
    } */


    #polarAreaChartContainer {
      display: inline-block;
      vertical-align: top;
      height: 290px;
    }

    .card {
      position: relative;
      margin: 2rem;
      background-color: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #e9ecef;
      padding: 1rem;
      width: calc(100% - 4rem);
    }

    .card .tabs {
      margin-top: 2rem;
    }

    .tabs-list {
      background-color: #EFF3EA;
      width: 100%;
      max-width: 20.5em;
      height: 2.5em;
      border-radius: 5px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto;
    }

    .tabs-trigger {
      height: 2em;
      width: 10em;
      border-radius: 3px;
      display: flex;
      justify-content: center;
      align-items: center;
      border: none;
      background-color: #F8FAFC;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .tabs-trigger:hover {
      background-color: #e2e6ea;
    }

    .recommendations {
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 1rem;
      margin: 1rem 0;
    }

    .heading {
      clear: both;
      margin-top: 1rem;
    }

    .header2 {
      padding-left: 1rem;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <?php include '../reusable/sidebar.php'; ?>
  <section class="panel">
    <?php include '../reusable/navbarNoSearch.html'; ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="table-header">
            <div class="title">
              <h2>Sales and Inventory Report</h2>
            </div>
          </div>
          <div class="table-header">
            <form>
              <label for="year">Year:</label>
              <select id="year" name="year">
                <option value="">All</option>
                <?php foreach ($years as $year): ?>
                  <option value="<?= $year ?>" <?= isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : '' ?>>
                    <?= $year ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="month">Month:</label>
              <select id="month" name="month">
                <option value="">All</option>
                <?php foreach ($months as $month): ?>
                  <option value="<?= $month ?>" <?= isset($_GET['month']) && $_GET['month'] == $month ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="day">Day:</label>
              <select id="day" name="day">
                <option value="">All</option>
                <?php foreach ($days as $day): ?>
                  <option value="<?= $day ?>" <?= isset($_GET['day']) && $_GET['day'] == $day ? 'selected' : '' ?>>
                    <?= $day ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="submit">Filter</button>
            </form>
          </div>
          <div class="col-md-12">
            <div class="container"
              style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">

              <?php
              if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day'])) {
                $filtered_year = $_GET['year'];
                $filtered_month = $_GET['month'];
                $filtered_day = $_GET['day'];
                $filtered_total_sum = 0;
                foreach ($orderdate_values as $key => $date) {
                  $year = date('Y', strtotime($date));
                  $month = date('m', strtotime($date));
                  $day = date('d', strtotime($date));
                  if (($filtered_year == '' || $year == $filtered_year) && ($filtered_month == '' || $month == $filtered_month) && ($filtered_day == '' || $day == $filtered_day)) {
                    $filtered_total_sum += $ordertotal_values[$key];
                  }
                }
                echo '<p>Filtered Total Sum: ' . $filtered_total_sum . '</p>';
              }
              ?>
              <?php
              if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day'])) {
                $filtered_year = $_GET['year'];
                $filtered_month = $_GET['month'];
                $filtered_day = $_GET['day'];

                if ($filtered_year == '' && $filtered_month == '' && $filtered_day == '') {
                  $filtered_ordertotal_values = $ordertotal_values;
                  $filtered_orderdate_values = $orderdate_values;
                  $chartLabel = "All Orders";
                } else {
                  $filtered_ordertotal_values = [];
                  $filtered_orderdate_values = [];
                  $chartLabel = "Orders";
                  if ($filtered_year != '') {
                    $chartLabel .= " in " . $filtered_year;
                  }
                  if ($filtered_month != '') {
                    $chartLabel .= " " . date('F', mktime(0, 0, 0, $filtered_month, 1));
                  }
                  if ($filtered_day != '') {
                    $chartLabel .= " on " . $filtered_day;
                  }
                  foreach ($orderdate_values as $key => $date) {
                    $year = date('Y', strtotime($date));
                    $month = date('m', strtotime($date));
                    $day = date('d', strtotime($date));
                    if (($filtered_year == '' || $year == $filtered_year) && ($filtered_month == '' || $month == $filtered_month) && ($filtered_day == '' || $day == $filtered_day)) {
                      $filtered_ordertotal_values[] = $ordertotal_values[$key];
                      $filtered_orderdate_values[] = $date;
                    }
                  }
                }
              }
              ?>
            </div>
          </div>
          <div class="chartscontainer">
            <div class="chartBox">
              <canvas id="myChart"></canvas>
            </div>

            <div class="rightchart">
              <div id="polarAreaChartContainer"
                style="background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 10px;overflow: visible; display: flex; flex-direction: column;">
                <canvas id="polarAreaChart"></canvas>
              </div>
              <div id="productSalesList"
                style="background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 10px;overflow: visible; display: flex; flex-direction: column;">
                <h4>Product Sales</h4>
                <ul>
                  <?php foreach ($chart_labels_chart as $index => $label) { ?>
                    <li>
                      <?= $label ?>
                      (<?= number_format($chart_data_chart[$index] / array_sum($chart_data_chart) * 100, 2) ?>%)
                    </li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div>


        </div>
      </div>

      <section style="margin-left: 0.1rem; margin-top: 1rem;" class="prescriptive">
        <div
          style="background-color: #fff; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 5px; border: 1px solid #ddd;">

          <div class="recomendations">
            <div class="heading" style="position: relative; margin: 1rem 0; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 5px; border: 1px solid #ddd; padding: 1rem; background-color: #f8f9fa;">
              <h2 style="text-align: center;" class="header2">Products Analysis and Recommendations</h2>
            </div>
            <?php
            $query = "SELECT * FROM customerorder";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
              $label = $row['orderdescription'];
              // retrieve other data here
            }
            ?>
            <table style="width:100%; border-collapse: collapse;">
              <thead>
                <tr>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Order Date</th>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Product Ordered</th>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Status</th>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Sales</th>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Recommendation</th>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Expected Monthly Sales</th>
                  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Expected Annual Sales</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Query to get all orders from the customerorder table
                $query = "SELECT orderdate, ordertotal, status, orderdescription FROM customerorder";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                  // Group orders by orderdate
                  $ordersByDate = array();
                  while ($row = mysqli_fetch_assoc($result)) {
                    $orderdate = $row['orderdate'];
                    if (!isset($ordersByDate[$orderdate])) {
                      $ordersByDate[$orderdate] = array();
                    }
                    $ordersByDate[$orderdate][] = $row;
                  }

                  // Output table rows
                  foreach ($ordersByDate as $orderdate => $orders) {
                    $totalSales = 0;
                    $productNames = array();
                    foreach ($orders as $order) {
                      $totalSales += $order['ordertotal'];
                      $orderdescription = $order['orderdescription'];
                      // Extract product names from orderdescription
                      $productNames = array_merge($productNames, explode(',', $orderdescription));
                    }

                    // Calculate daily sales, expected monthly sales, and expected annual sales
                    $days_in_month = date('t', strtotime($orderdate));
                    $days_remaining_in_month = $days_in_month - date('j', strtotime($orderdate)) + 1;
                    $daily_sales = $totalSales;
                    $expected_monthly_sales = $daily_sales * $days_remaining_in_month;
                    $expected_annual_sale = $expected_monthly_sales * 12;
                    // Recommendation logic
                    $holiday_season = false;
                    $holiday_months = array('10' => 'Halloween', '12' => 'Christmas');
                    $current_month = date('m', strtotime($orderdate));
                    if (isset($holiday_months[$current_month])) {
                      $holiday_season = true;
                    }
                    $recommendation = ($expected_monthly_sales <= $totalSales)
                      ? "Offer targeted promotions to boost sales."
                      : ($holiday_season
                          ? "Increase inventory for holiday demand. Launch seasonal promotions or themed campaigns."
                          : "Maintain current strategy; monitor for continued success.");

                    // Output table rows
                    echo "<tr style='border-bottom: 1px solid #ddd;'>";
                    echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>" . date('F j, Y', strtotime($orderdate)) . "</td>";
                    echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>" . implode(', ', $productNames) . "</td>";
                    echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>{$orders[0]['status']}</td>";
                    echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>&#8369;" . number_format($totalSales, 2) . "</td>";
                    echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>$recommendation</td>";
                    echo "<td style='border-right: 1px solid #ddd; padding: 8px; text-align: center;'>&#8369;" . number_format($expected_monthly_sales, 2) . "</td>";
                    echo "<td style='padding: 8px; text-align: center;'>&#8369;" . number_format($expected_annual_sale, 2) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='7' style='text-align: center;'>No orders found.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>


        </div>
      </section>

  </section>



  </div>

  <script>
    const ctx = document.getElementById('myChart');
    const chartData = <?= json_encode($filtered_ordertotal_values ?? $ordertotal_values) ?>;
    const chartBackgroundColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 0.7)' : 'rgba(255, 99, 132, 0.8)');
    const chartBorderColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 99, 132, 1)');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($filtered_orderdate_values ?? $orderdate_values) ?>,
        datasets: [{
          label: 'Total Sales',
          data: chartData,
          backgroundColor: chartBackgroundColor,
          borderColor: chartBorderColor,
          borderWidth: 1,
          borderRadius: 5, // Rounded edges
          borderSkipped: false // Apply rounded edges to all bars
        }]
      },
      options: {
        plugins: {
          title: {
            display: true,
            text: '<?= $chartLabel ?? "All Orders" ?>',
            color: 'black'
          },
          legend: {
            labels: {
              color: 'black'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: 'black'
            }
          },
          x: {
            ticks: {
              color: 'black'
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });
    const ctxPolar = document.getElementById('polarAreaChart').getContext('2d');
    const polarChart = new Chart(ctxPolar, {
      type: 'pie',
      data: {
        labels: <?= json_encode($chart_labels_chart) ?>,
        datasets: [{
          label: 'Product Sales',
          data: <?= json_encode($chart_data_chart) ?>,
          backgroundColor: [
            'rgba(34, 49, 63, 0.8)', // Darker and more solid color
            'rgba(54, 162, 235, 0.8)', // Darker and more solid color
            'rgba(255, 206, 86, 0.8)', // Darker and more solid color
            'rgba(75, 192, 192, 0.8)', // Darker and more solid color
            'rgba(153, 102, 255, 0.8)', // Darker and more solid color
            'rgba(255, 159, 64, 0.8)', // Darker and more solid color
            'rgba(199, 199, 199, 0.8)', // Darker and more solid color
            'rgba(83, 102, 255, 0.8)', // Darker and more solid color
            'rgba(255, 205, 86, 0.8)', // Darker and more solid color
            'rgba(75, 192, 192, 0.8)' // Darker and more solid color
          ],
          borderColor: [
            'rgba(34, 49, 63, 1)', // Darker and more solid color
            'rgba(54, 162, 235, 1)', // Darker and more solid color
            'rgba(255, 206, 86, 1)', // Darker and more solid color
            'rgba(75, 192, 192, 1)', // Darker and more solid color
            'rgba(153, 102, 255, 1)', // Darker and more solid color
            'rgba(255, 159, 64, 1)', // Darker and more solid color
            'rgba(199, 199, 199, 1)', // Darker and more solid color
            'rgba(83, 102, 255, 1)',
            'rgba(255, 205, 86, 1)', // Darker and more solid color
            'rgba(75, 192, 192, 1)' // Darker and more solid color
          ],
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            labels: {
              color: 'black'
            }
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });


    // Function to update the polar chart data
    function updateChartData(year, month, day) {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', 'supervisor/sales.php?updateChartData=true&year=' + year + '&month=' + month + '&day=' + day, true);
      xhr.onload = function () {
        if (xhr.status === 200) {
          var data = JSON.parse(xhr.responseText);
          polarChart.data.labels = data.labels.slice(0, 10);
          polarChart.data.datasets[0].data = data.data.slice(0, 10);
          polarChart.update();
        }
      };
      xhr.send();
    }
  </script>


  <?php include '../reusable/footer.php'; ?>
</body>

</html>