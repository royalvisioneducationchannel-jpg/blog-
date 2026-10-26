<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=1080, height=1920" />
    <script src="assets/vendor/gsap.min.js"></script>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      html,
      body {
        margin: 0;
        width: 1080px;
        height: 1920px;
        overflow: hidden;
        background: #0b0c10;
      }
      #root {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        font-family: "Inter", sans-serif;
      }
      /* Two camera wrappers move together: footage below the wall type, cutout + callouts above it */
      .cam {
        position: absolute;
        inset: 0;
        transform-origin: 0 0;
      }
      #cam {
        z-index: 1;
      }
      #camfg {
        z-index: 3;
      }
      .fill {
        position: absolute;
        inset: 0;
        width: 1080px;
        height: 1920px;
        object-fit: cover;
      }
      .scene {
        position: absolute;
        inset: 0;
      }
      #behind {
        z-index: 2;
      }
      #stage {
        z-index: 5;
      }
      #captions {
        z-index: 6;
      }
      .co {
        position: absolute;
        inset: 0;
      }
      .co-svg {
        position: absolute;
        inset: 0;
        overflow: visible;
      }
      .ring,
      .lead,
      .tick {
        fill: none;
        stroke: #121418;
        stroke-width: 3;
      }
      .ring-under,
      .lead-under {
        fill: none;
        stroke: rgba(255, 255, 255, 0.9);
        stroke-width: 7;
      }
      .tick {
        stroke-width: 4;
      }
      .co-lab {
        position: absolute;
        width: 460px;
        height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding-bottom: 14px;
        color: #121418;
      }
      .co-kick {
        font-weight: 700;
        font-size: 28px;
        letter-spacing: 0.34em;
        text-transform: uppercase;
        color: #3d424c;
      }
      .co-name {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-top: 6px;
        font-weight: 900;
        font-size: 86px;
        line-height: 1;
        letter-spacing: -0.035em;
      }
      .co-sw {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        box-shadow: inset 0 0 0 2px rgba(18, 20, 24, 0.25);
      }
    </style>
  </head>
  <body>
    <div id="root" data-composition-id="main" data-start="0" data-duration="33" data-width="1080" data-height="1920">
      <div id="cam" class="cam">
        <video id="footage" class="clip fill" src="assets/media/footage.mp4" muted playsinline data-start="0" data-duration="30.35" data-track-index="0"></video>
      </div>

      <div id="behind" class="scene" data-composition-id="behind" data-composition-src="compositions/behind.html" data-start="0" data-duration="30.35" data-track-index="1" data-track-kind="graphics" data-width="1080" data-height="1920"></div>

      <div id="camfg" class="cam">
        <div id="matte-wrap" class="scene">
          <video id="matte" class="clip fill" src="assets/media/person-matte.webm" muted playsinline data-start="0" data-media-start="0" data-duration="30.35" data-track-index="2" data-layout-allow-occlusion></video>
        </div>
<!--CALLOUTS-->
      </div>

      <div id="stage" class="scene" data-composition-id="stage" data-composition-src="compositions/stage.html" data-start="0" data-duration="33" data-track-index="3" data-track-kind="graphics" data-width="1080" data-height="1920"></div>
      <div id="captions" class="scene" data-composition-id="captions" data-composition-src="compositions/captions.html" data-start="0" data-duration="30.2" data-track-index="6" data-track-kind="captions" data-width="1080" data-height="1920"></div>

      <audio id="voice" class="clip" src="assets/audio/voice.wav" data-start="0" data-duration="30.35" data-track-index="7" data-volume="1"></audio>
      <audio id="music" class="clip" src="assets/audio/music-hazy-after-hours.mp3" data-start="0" data-duration="33" data-media-start="3.14" data-track-index="8" data-volume="0.25" data-fade-in="0.4" data-fade-out="1.4"
        data-automation='{"version":1,"lanes":[{"target":"volume","points":[{"t":0,"v":0.4},{"t":25.45,"v":0.4},{"t":25.75,"v":0.06},{"t":26.84,"v":0.06},{"t":26.9,"v":0.45},{"t":29.6,"v":0.45},{"t":30.0,"v":1}]}]}'></audio>
      <audio id="sfx-rise" class="clip" src="assets/audio/whoosh-short.mp3" data-start="0.12" data-duration="0.57" data-track-index="9" data-volume="0.18"></audio>
      <audio id="sfx-match" class="clip" src="assets/audio/whoosh-short.mp3" data-start="2.2" data-duration="0.57" data-track-index="9" data-volume="0.25"></audio>
      <audio id="sfx-order" class="clip" src="assets/audio/impact-bass-1.mp3" data-start="3.89" data-duration="2.1" data-track-index="10" data-volume="0.15"></audio>
      <audio id="sfx-colors" class="clip" src="assets/audio/whoosh-short.mp3" data-start="6.5" data-duration="0.57" data-track-index="9" data-volume="0.15"></audio>
      <audio id="sfx-c1" class="clip" src="assets/audio/click-soft.mp3" data-start="11.36" data-duration="0.36" data-track-index="11" data-volume="0.3"></audio>
      <audio id="sfx-c2" class="clip" src="assets/audio/click-soft.mp3" data-start="13.32" data-duration="0.36" data-track-index="11" data-volume="0.3"></audio>
      <audio id="sfx-c3" class="clip" src="assets/audio/click-soft.mp3" data-start="14.74" data-duration="0.36" data-track-index="11" data-volume="0.3"></audio>
      <audio id="sfx-c4" class="clip" src="assets/audio/click-soft.mp3" data-start="16.27" data-duration="0.36" data-track-index="12" data-volume="0.22"></audio>
      <audio id="sfx-c5" class="clip" src="assets/audio/click-soft.mp3" data-start="17.14" data-duration="0.36" data-track-index="11" data-volume="0.3"></audio>
      <audio id="sfx-riser" class="clip" src="assets/audio/riser.mp3" data-start="19.1" data-duration="2.0" data-media-start="8.0" data-track-index="13" data-volume="0.22" data-fade-in="0.6"></audio>
      <audio id="sfx-off" class="clip" src="assets/audio/impact-bass-1.mp3" data-start="21.1" data-duration="2.1" data-track-index="10" data-volume="0.45"></audio>
      <audio id="sfx-web" class="clip" src="assets/audio/whoosh-short.mp3" data-start="22.3" data-duration="0.57" data-track-index="9" data-volume="0.22"></audio>
      <audio id="sfx-wipe" class="clip" src="assets/audio/whoosh-short.mp3" data-start="20.85" data-duration="0.57" data-track-index="12" data-volume="0.28"></audio>
      <audio id="sfx-bars" class="clip" src="assets/audio/whoosh-short.mp3" data-start="25.28" data-duration="0.57" data-track-index="13" data-volume="0.14"></audio>
      <audio id="sfx-line" class="clip" src="assets/audio/click-soft.mp3" data-start="26.6" data-duration="0.36" data-track-index="12" data-volume="0.2"></audio>
      <audio id="sfx-ship" class="clip" src="assets/audio/impact-bass-1.mp3" data-start="26.88" data-duration="2.1" data-track-index="13" data-volume="0.35"></audio>
      <audio id="sfx-truck" class="clip" src="assets/audio/whoosh-short.mp3" data-start="27.05" data-duration="0.57" data-track-index="9" data-volume="0.2"></audio>
      <audio id="sfx-hero" class="clip" src="assets/audio/impact-bass-1.mp3" data-start="29.75" data-duration="2.1" data-track-index="10" data-volume="0.3"></audio>
    </div>
    <script>
      const tl = gsap.timeline({ paused: true });
      const E = "power3.out";
      const io = "power3.inOut";
      const CAM = ["#cam", "#camfg"];
      // origin is top-left; x = -(s-1)*540, y = -(s-1)*500 keeps the push centred on the speaker
      const at = (s) => ({ scale: s, x: -(s - 1) * 540, y: -(s - 1) * 500 });
      tl.set(CAM, at(1), 0);
      // ORDER: slow push on the speaker
      tl.to(CAM, { ...at(1.06), duration: 3.4, ease: "none" }, 3.0);
      // COLORS: a lateral glide at a fixed size; the wall type drifts less, so it reads as its own plane
      tl.to(CAM, { scale: 1.08, x: 0, y: -40, duration: 0.6, ease: io }, 6.48);
      tl.to(CAM, { scale: 1.08, x: -86, y: -40, duration: 4.2, ease: "sine.inOut" }, 7.08);
      // Colour reveals: the camera leans toward whichever box is being named (track data)
      const S = 1.12, YC = -(S - 1) * 500;
      const follow = (bx) => Math.max(-(S - 1) * 1080, Math.min(0, -(S - 1) * 540 - (bx - 540) * 0.35));
      tl.to(CAM, { scale: S, x: follow(861), y: YC, duration: 0.6, ease: io }, 11.36);
      tl.to(CAM, { x: follow(560), duration: 1.2, ease: "sine.inOut" }, 11.9);
      tl.to(CAM, { x: follow(410), duration: 0.6, ease: io }, 13.32);
      tl.to(CAM, { x: follow(560), duration: 0.6, ease: io }, 14.74);
      tl.to(CAM, { x: follow(684), duration: 0.6, ease: io }, 16.27);
      tl.to(CAM, { x: follow(282), duration: 0.6, ease: io }, 17.14);
      // OFFER: back to wide, then a tension push that the 20% cut releases
      tl.to(CAM, { ...at(1.0), duration: 0.45, ease: io }, 18.73);
      tl.to(CAM, { ...at(1.14), duration: 1.65, ease: "power2.in" }, 19.2);
      // WEBSITE: the reverse move, a pull-out
      tl.set(CAM, at(1.14), 22.3);
      tl.to(CAM, { ...at(1.02), duration: 2.9, ease: "power2.out" }, 22.66);
      tl.set(CAM, at(1), 29.0);

      //CALLOUTS
      window.__timelines["main"] = tl;
      tl.seek(0);
    </script>
  </body>
</html>
