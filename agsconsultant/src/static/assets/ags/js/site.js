/* AGS Consultant — site scripts */
(function () {
  "use strict";
  var doc = document.documentElement;
  doc.classList.remove("no-js");

  /* Sticky header shadow */
  var header = document.querySelector(".site-header");
  function onScroll() {
    if (header) header.classList.toggle("is-scrolled", window.scrollY > 8);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* Mobile navigation */
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("site-nav");
  function setNav(open) {
    document.body.classList.toggle("nav-open", open);
    if (toggle) toggle.setAttribute("aria-expanded", String(open));
    if (open && header) {
      document.body.style.setProperty("--nav-top", header.getBoundingClientRect().bottom + "px");
    }
  }
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      setNav(!document.body.classList.contains("nav-open"));
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        setNav(false);
        document.querySelectorAll(".nav__item--has-dd.is-open").forEach(function (el) {
          el.classList.remove("is-open");
          el.querySelector(".nav__link").setAttribute("aria-expanded", "false");
        });
      }
    });
    window.addEventListener("resize", function () {
      if (window.innerWidth > 1080) setNav(false);
    });
  }

  /* Dropdown (click/tap + keyboard) */
  document.querySelectorAll(".nav__item--has-dd > .nav__link").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      var item = btn.parentElement;
      var open = !item.classList.contains("is-open");
      item.classList.toggle("is-open", open);
      btn.setAttribute("aria-expanded", String(open));
    });
  });
  document.addEventListener("click", function (e) {
    document.querySelectorAll(".nav__item--has-dd.is-open").forEach(function (item) {
      if (!item.contains(e.target) && window.innerWidth > 1080) {
        item.classList.remove("is-open");
        item.querySelector(".nav__link").setAttribute("aria-expanded", "false");
      }
    });
  });

  /* Reveal on scroll */
  var reveals = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && reveals.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add("is-visible"); });
  }

  /* Count-up numbers */
  var counters = document.querySelectorAll("[data-count]");
  function runCount(el) {
    var target = parseInt(el.getAttribute("data-count"), 10) || 0;
    var start = null;
    var dur = 1200;
    function frame(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }
  if ("IntersectionObserver" in window && counters.length && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    var co = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { runCount(entry.target); co.unobserve(entry.target); }
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { co.observe(el); });
  }

  /* Forms: validation + async submit with graceful fallback */
  var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  var phoneRe = /^[0-9+\-\s()]{7,20}$/;

  function fieldError(field, msg) {
    var wrap = field.closest(".field");
    if (!wrap) return;
    var err = wrap.querySelector(".field__error");
    wrap.classList.toggle("has-error", !!msg);
    field.setAttribute("aria-invalid", msg ? "true" : "false");
    if (err) err.textContent = msg || "";
  }

  function validate(field) {
    if (field.type === "radio") {
      var group = field.form ? field.form.querySelectorAll('input[name="' + field.name + '"]') : [field];
      var anyRequired = false, checked = false;
      Array.prototype.forEach.call(group, function (r) { if (r.required) anyRequired = true; if (r.checked) checked = true; });
      if (anyRequired && !checked) { fieldError(field, "Please choose an option."); return false; }
      fieldError(field, "");
      return true;
    }
    var v = (field.value || "").trim();
    if (field.required && !v) { fieldError(field, "This field is required."); return false; }
    if (v && field.type === "email" && !emailRe.test(v)) { fieldError(field, "Please enter a valid email address."); return false; }
    if (v && field.type === "tel" && !phoneRe.test(v)) { fieldError(field, "Please enter a valid phone number."); return false; }
    fieldError(field, "");
    return true;
  }

  document.querySelectorAll("form[data-ags-form]").forEach(function (form) {
    var ts = form.querySelector('input[name="_ts"]');
    if (ts) ts.value = String(Math.floor(Date.now() / 1000));
    var status = form.querySelector(".form-status");
    var btn = form.querySelector('button[type="submit"]');
    var fields = form.querySelectorAll("input:not([type=hidden]):not(.hp input), select, textarea");

    fields.forEach(function (f) {
      f.addEventListener("blur", function () { if (f.value && f.type !== "radio") validate(f); });
      if (f.type === "radio") f.addEventListener("change", function () { validate(f); });
      f.addEventListener("input", function () {
        if (f.closest(".field") && f.closest(".field").classList.contains("has-error")) validate(f);
      });
    });

    function showStatus(type, msg) {
      if (!status) return;
      status.className = "form-status is-" + type;
      status.innerHTML = "";
      var span = document.createElement("span");
      span.textContent = msg;
      status.appendChild(span);
      status.setAttribute("role", type === "error" ? "alert" : "status");
    }

    form.addEventListener("submit", function (e) {
      var ok = true, first = null;
      fields.forEach(function (f) {
        if (f.closest(".hp")) return;
        if (!validate(f)) { ok = false; if (!first) first = f; }
      });
      if (!ok) { e.preventDefault(); if (first) first.focus(); return; }
      if (!window.fetch || !window.FormData) return; // normal POST fallback

      e.preventDefault();
      var label = btn ? btn.innerHTML : "";
      if (btn) { btn.disabled = true; btn.textContent = "Sending…"; }

      fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        headers: { "Accept": "application/json", "X-Requested-With": "fetch" }
      })
        .then(function (r) { return r.json().catch(function () { return { ok: false }; }); })
        .then(function (data) {
          if (data && data.ok) {
            if (form.getAttribute("data-redirect")) { window.location.href = form.getAttribute("data-redirect"); return; }
            form.reset();
            if (ts) ts.value = String(Math.floor(Date.now() / 1000));
            showStatus("success", data.message || "Thank you! Our team will contact you shortly.");
          } else {
            showStatus("error", (data && data.message) || "Sorry, something went wrong. Please call or WhatsApp us on +92 300 1959995.");
          }
        })
        .catch(function () {
          showStatus("error", "Network error. Please try again, or WhatsApp us on +92 300 1959995.");
        })
        .then(function () {
          if (btn) { btn.disabled = false; btn.innerHTML = label; }
        });
    });
  });

  /* Footer year */
  document.querySelectorAll("[data-year]").forEach(function (el) { el.textContent = new Date().getFullYear(); });
})();
