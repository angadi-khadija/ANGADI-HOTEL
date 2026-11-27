document.addEventListener("DOMContentLoaded", function () {
  var menuButton = document.querySelector(".menu-toggle");
  var siteNav = document.querySelector(".site-nav");

  if (menuButton && siteNav) {
    menuButton.addEventListener("click", function () {
      var isOpen = siteNav.classList.toggle("open");
      menuButton.setAttribute("aria-expanded", String(isOpen));
    });
  }

  var bookingForm = document.getElementById("booking-form");
  if (bookingForm) {
    var checkin = bookingForm.elements.checkin;
    var checkout = bookingForm.elements.checkout;
    var today = new Date();
    var localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
    checkin.min = localToday;
    checkout.min = localToday;

    checkin.addEventListener("change", function () {
      checkout.min = checkin.value || localToday;
      if (checkout.value && checkout.value <= checkin.value) {
        checkout.value = "";
      }
    });

    bookingForm.addEventListener("submit", function (event) {
      event.preventDefault();
      if (checkout.value <= checkin.value) {
        checkout.setCustomValidity("Check-out must be after check-in.");
        checkout.reportValidity();
        return;
      }
      checkout.setCustomValidity("");

      var data = new FormData(bookingForm);
      var enquiry = {
        name: data.get("name"),
        email: data.get("email"),
        checkin: data.get("checkin"),
        checkout: data.get("checkout"),
        room: data.get("room"),
        guests: data.get("guests")
      };
      var enquiries;
      try {
        enquiries = JSON.parse(localStorage.getItem("angadi-demo-enquiries") || "[]");
        if (!Array.isArray(enquiries)) enquiries = [];
        enquiries.push(enquiry);
        localStorage.setItem("angadi-demo-enquiries", JSON.stringify(enquiries));
      } catch (error) {
        window.alert("The demo enquiry could not be saved in this browser. Please try again.");
        return;
      }

      var confirmation = document.getElementById("booking-confirmation");
      confirmation.innerHTML = "<h2>Thank you, " + escapeHtml(enquiry.name) + ".</h2><p>Your demo enquiry for a " + escapeHtml(enquiry.room) + " is saved in this browser only. The hotel has not been contacted.</p><button class=\"button button-dark\" type=\"button\" id=\"new-enquiry\">Make another enquiry <span>→</span></button>";
      bookingForm.hidden = true;
      confirmation.hidden = false;
      document.getElementById("new-enquiry").addEventListener("click", function () {
        bookingForm.reset();
        checkout.min = localToday;
        confirmation.hidden = true;
        bookingForm.hidden = false;
      });
    });
  }

  var dateLabel = document.getElementById("today-label");
  if (dateLabel) {
    dateLabel.textContent = "TODAY AT ANGADI · " + new Intl.DateTimeFormat("en", { weekday: "long", month: "long", day: "numeric", year: "numeric" }).format(new Date()).toUpperCase();
  }

  var views = document.querySelectorAll(".dash-view");
  if (views.length) {
    var titles = { overview: "Overview", rooms: "Rooms", reservations: "Reservations", guests: "Guests" };
    function showView(name) {
      if (!titles[name]) return;
      views.forEach(function (view) { view.classList.toggle("active", view.id === "view-" + name); });
      document.querySelectorAll(".side-link").forEach(function (link) {
        var active = link.dataset.view === name;
        link.classList.toggle("active", active);
        if (active) link.setAttribute("aria-current", "page");
        else link.removeAttribute("aria-current");
      });
      document.getElementById("dashboard-title").textContent = titles[name];
      if (window.location.hash !== "#" + name) history.replaceState(null, "", "#" + name);
    }

    document.querySelectorAll(".side-link").forEach(function (link) {
      link.addEventListener("click", function (event) {
        event.preventDefault();
        showView(link.dataset.view);
      });
    });
    document.querySelectorAll("[data-open-view]").forEach(function (button) {
      button.addEventListener("click", function () { showView(button.dataset.openView); });
    });
    showView(window.location.hash.slice(1) || "overview");

    var saved = [];
    try {
      saved = JSON.parse(localStorage.getItem("angadi-demo-enquiries") || "[]");
      if (!Array.isArray(saved)) saved = [];
    } catch (error) {
      saved = [];
    }
    var count = document.getElementById("enquiry-count");
    if (count && saved.length) count.textContent = String(3 + saved.length).padStart(2, "0");
    var reservationsBody = document.querySelector("#reservations-table tbody");
    var guestsBody = document.querySelector("#guests-table tbody");
    saved.slice().reverse().forEach(function (item) {
      var arrival = escapeHtml(item.checkin);
      var departure = escapeHtml(item.checkout);
      var room = escapeHtml(item.room);
      var name = escapeHtml(item.name);
      var email = escapeHtml(item.email);
      var guestCount = escapeHtml(item.guests);
      var row = document.createElement("tr");
      row.innerHTML = "<td><strong>" + name + "</strong><small>" + email + "</small></td><td>" + room + "</td><td>" + arrival + " – " + departure + "</td><td>" + guestCount + "</td><td><span class=\"status status-gold\">New enquiry</span></td>";
      reservationsBody.insertBefore(row, reservationsBody.firstChild);
      var guestRow = document.createElement("tr");
      guestRow.innerHTML = "<td><strong>" + name + "</strong></td><td>" + email + "</td><td>" + arrival + "</td><td>New</td>";
      guestsBody.insertBefore(guestRow, guestsBody.firstChild);
    });
  }

  var roomFilter = document.getElementById("room-filter");
  if (roomFilter) {
    roomFilter.addEventListener("change", function () {
      document.querySelectorAll("#rooms-table tbody tr").forEach(function (row) {
        row.hidden = roomFilter.value !== "all" && row.dataset.status !== roomFilter.value;
      });
    });
  }

  var guestSearch = document.getElementById("guest-search");
  if (guestSearch) {
    guestSearch.addEventListener("input", function () {
      var query = guestSearch.value.trim().toLowerCase();
      document.querySelectorAll("#guests-table tbody tr").forEach(function (row) {
        row.hidden = !row.textContent.toLowerCase().includes(query);
      });
    });
  }
});

function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, function (character) {
    return { "&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;", "'": "&#39;" }[character];
  });
}
