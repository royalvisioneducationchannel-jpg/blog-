<template>
  <style>
    /* Kinetic speech type: words rise out of masks as they are spoken; product words carry the accent */
    #root {
      position: absolute;
      inset: 0;
      pointer-events: none;
    }
    .cgroup {
      position: absolute;
      left: 0;
      right: 0;
      top: 1228px;
      padding: 0 80px;
      text-align: center;
      overflow: visible;
    }
    .cline {
      display: inline-block;
      max-width: 920px;
      font-family: "Inter", sans-serif;
      font-weight: 700;
      font-size: 54px;
      line-height: 1.12;
      letter-spacing: -0.02em;
      color: #ffffff;
      text-shadow: 0 4px 24px rgba(0, 0, 0, 0.55);
    }
    .m {
      position: relative;
      display: inline-block;
      overflow: hidden;
      vertical-align: bottom;
      padding: 0 0.08em 0.16em;
      margin: 0;
    }
    .cw {
      display: inline-block;
    }
    .m.key {
      font-weight: 900;
      font-size: 1.32em;
      letter-spacing: -0.035em;
      color: #f3d417;
    }
    .ul {
      position: absolute;
      left: 0.08em;
      right: 0.08em;
      bottom: 0.06em;
      height: 6px;
      border-radius: 3px;
      background: #f3d417;
      transform-origin: 0 50%;
    }
  </style>
  <div id="root" data-composition-id="captions" data-width="1080" data-height="1920">
<!--GROUPS-->
  </div>
  <script>
    const tl = gsap.timeline({ paused: true });
    function grp(sel, s, e) {
      tl.to(sel, { opacity: 0, y: -14, duration: 0.16, ease: "power2.in" }, e - 0.16);
    }
    function hit(sel, s) {
      tl.fromTo(sel, { yPercent: 115, rotation: 5 }, { yPercent: 0, rotation: 0, duration: 0.34, ease: "expo.out" }, Math.max(0, s - 0.04));
    }
    function key(sel, s) {
      tl.fromTo(sel, { scaleX: 0 }, { scaleX: 1, duration: 0.3, ease: "power3.out" }, s + 0.14);
    }
    //STEPS
    window.__timelines["captions"] = tl;
  </script>
</template>
