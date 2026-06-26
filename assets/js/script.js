// Script tambahan untuk fitur interaktif

// Fungsi untuk konfirmasi hapus
function confirmDelete(message) {
  return confirm(message || "Apakah Anda yakin ingin menghapus data ini?");
}

// Format tanggal
function formatDate(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

// Toggle sidebar di mobile
document.addEventListener("DOMContentLoaded", function () {
  // Sidebar toggle
  const sidebarToggle = document.querySelector('[data-bs-toggle="collapse"]');
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", function () {
      document.querySelector(".sidebar").classList.toggle("show");
    });
  }

  // Auto-hide alert setelah 5 detik
  document.querySelectorAll(".alert").forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });

  // Tooltip
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]'),
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Highlight row saat dihover
  document.querySelectorAll(".table-hover tbody tr").forEach(function (row) {
    row.addEventListener("mouseenter", function () {
      this.style.cursor = "pointer";
    });
  });
});

// Fungsi untuk print laporan
function printReport() {
  window.print();
}

// Validasi form
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return true;

  const inputs = form.querySelectorAll("input[required], select[required]");
  let valid = true;

  inputs.forEach(function (input) {
    if (!input.value.trim()) {
      input.classList.add("is-invalid");
      valid = false;
    } else {
      input.classList.remove("is-invalid");
    }
  });

  return valid;
}

// Reset form
function resetForm(formId) {
  const form = document.getElementById(formId);
  if (form) {
    form.reset();
    form.querySelectorAll(".is-invalid").forEach(function (el) {
      el.classList.remove("is-invalid");
    });
  }
}

// Search dengan debounce
function debounceSearch() {
  let timeout;
  return function (searchInput, callback) {
    clearTimeout(timeout);
    timeout = setTimeout(function () {
      callback(searchInput.value);
    }, 300);
  };
}

// Statistik animasi
function animateCounter(element, target, duration = 1000) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(function () {
    start += step;
    if (start >= target) {
      start = target;
      clearInterval(timer);
    }
    element.textContent = Math.floor(start);
  }, 16);
}

// Inisialisasi counter di dashboard
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".stat-card .card-title").forEach(function (el) {
    const target = parseInt(el.textContent);
    if (target > 0) {
      animateCounter(el, target, 800);
    }
  });
});

// Konfirmasi logout
document.querySelectorAll('a[href*="logout"]').forEach(function (link) {
  link.addEventListener("click", function (e) {
    if (!confirm("Apakah Anda yakin ingin logout?")) {
      e.preventDefault();
    }
  });
});
