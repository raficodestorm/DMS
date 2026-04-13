//  -------------------------------- for sale chart --------------------------------

// public/js/sales.js
document.addEventListener("DOMContentLoaded", function() {
  const canvas = document.getElementById("monthlyChart");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
      datasets: [{
        label: "Bookings",
        data: [120,95,110,130,125,140,100,115,90,105,98,150],
        backgroundColor: "#ff0000",
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { beginAtZero: true, grid: { color: "#eee" } }, x: { grid: { display: false } } },
      plugins: { legend: { display: false }, tooltip: { backgroundColor: "#2c3e50", titleColor: "#fff", bodyColor: "#fff" } }
    }
  });
});
