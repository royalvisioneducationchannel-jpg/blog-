import { Canvas, useFrame, useThree } from '@react-three/fiber';
import { RoundedBox, useTexture } from '@react-three/drei';
import { Suspense, useEffect, useMemo, useRef, type MutableRefObject } from 'react';
import * as THREE from 'three';

export type SceneRefs = {
  intro: MutableRefObject<{ v: number }>;
  away: MutableRefObject<{ v: number }>;
  pointer: MutableRefObject<{ x: number; y: number }>;
  invalidate: MutableRefObject<(() => void) | null>;
};

type PanelDef = {
  src: string;
  aspect: number; // image width / height
  width: number;
  pos: [number, number, number];
  rot: [number, number, number];
  browser: boolean;
  period: number; // seconds per float cycle
  phase: number;
  outward: [number, number];
  delay: number; // intro stagger (0..1 of intro progress)
};

const PANELS: PanelDef[] = [
  { src: './img/store-desktop.webp', aspect: 1280 / 900, width: 2.35, pos: [-1.6, 1.2, -0.9], rot: [-0.05, 0.24, 0.01], browser: true, period: 9, phase: 0, outward: [-1.4, 0.6], delay: 0.1 },
  { src: './img/law-desktop.webp', aspect: 1280 / 900, width: 2.05, pos: [1.85, 1.55, -2.1], rot: [-0.04, -0.28, -0.01], browser: true, period: 11, phase: 1.7, outward: [1.5, 0.7], delay: 0.25 },
  { src: './img/meta-results.webp', aspect: 1200 / 900, width: 1.6, pos: [-1.75, -1.45, 0.15], rot: [0.04, 0.2, -0.035], browser: false, period: 8, phase: 3.1, outward: [-1.3, -0.8], delay: 0.4 },
];

const BAR = 0.11;
const DEG = Math.PI / 180;

function useShadowTexture() {
  return useMemo(() => {
    const c = document.createElement('canvas');
    c.width = c.height = 128;
    const g = c.getContext('2d')!;
    const grd = g.createRadialGradient(64, 64, 8, 64, 64, 64);
    grd.addColorStop(0, 'rgba(0,0,0,0.55)');
    grd.addColorStop(1, 'rgba(0,0,0,0)');
    g.fillStyle = grd;
    g.fillRect(0, 0, 128, 128);
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    return t;
  }, []);
}

function Panel({ def, refs, motion, shadow }: { def: PanelDef; refs: SceneRefs; motion: boolean; shadow: THREE.Texture }) {
  const tex = useTexture(def.src);
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.anisotropy = 4;
  const group = useRef<THREE.Group>(null!);
  const mats = useRef<THREE.Material[]>([]);
  const h = def.width / def.aspect;
  const totalH = h + (def.browser ? BAR : 0);

  useEffect(() => {
    const list: THREE.Material[] = [];
    group.current.traverse((o) => {
      const m = (o as THREE.Mesh).material as THREE.Material | undefined;
      if (m) {
        m.transparent = true;
        m.needsUpdate = true;
        list.push(m);
      }
    });
    mats.current = list;
  }, []);

  useFrame(({ clock }) => {
    const t = clock.getElapsedTime();
    const intro = THREE.MathUtils.clamp((refs.intro.current.v - def.delay) / (1 - def.delay), 0, 1);
    const e = 1 - Math.pow(1 - intro, 3);
    const away = refs.away.current.v;
    const p = refs.pointer.current;
    const float = motion ? Math.sin((t / def.period) * Math.PI * 2 + def.phase) * 0.07 : 0;
    const depth = 1 + def.pos[2] * -0.25; // farther panels drift less
    const g = group.current;
    const targetX = def.pos[0] + def.outward[0] * away + (motion ? p.x * 0.12 / depth : 0);
    const targetY = def.pos[1] + def.outward[1] * away + float + (motion ? -p.y * 0.08 / depth : 0) - (1 - e) * 0.5;
    g.position.x += (targetX - g.position.x) * 0.08;
    g.position.y += (targetY - g.position.y) * 0.08;
    g.position.z = def.pos[2] - (1 - e) * 1.2;
    const tiltX = motion ? p.y * 3 * DEG : 0;
    const tiltY = motion ? p.x * 3 * DEG : 0;
    g.rotation.x += (def.rot[0] + tiltX - g.rotation.x) * 0.07;
    g.rotation.y += (def.rot[1] + tiltY - g.rotation.y) * 0.07;
    g.rotation.z = def.rot[2];
    const s = 0.92 + 0.08 * e;
    g.scale.setScalar(s);
    const op = e * (1 - away * 0.75);
    for (const m of mats.current) m.opacity = op;
  });

  return (
    <group ref={group} position={def.pos} rotation={def.rot}>
      <mesh position={[0.16, -0.22, -0.2]}>
        <planeGeometry args={[def.width * 1.35, totalH * 1.45]} />
        <meshBasicMaterial map={shadow} transparent depthWrite={false} />
      </mesh>
      <RoundedBox args={[def.width + 0.06, totalH + 0.06, 0.05]} radius={0.04} smoothness={3} position={[0, 0, -0.03]}>
        <meshStandardMaterial color="#13203a" roughness={0.55} metalness={0.15} />
      </RoundedBox>
      {def.browser && (
        <group position={[0, h / 2, 0.001]}>
          <mesh>
            <planeGeometry args={[def.width, BAR]} />
            <meshBasicMaterial color="#1c2a48" />
          </mesh>
          {[0, 1, 2].map((i) => (
            <mesh key={i} position={[-def.width / 2 + 0.08 + i * 0.07, 0, 0.002]}>
              <circleGeometry args={[0.018, 16]} />
              <meshBasicMaterial color={['#ff6b6b', '#f5c451', '#4ade80'][i]} />
            </mesh>
          ))}
        </group>
      )}
      <mesh position={[0, def.browser ? -BAR / 2 : 0, 0.002]}>
        <planeGeometry args={[def.width, h]} />
        <meshBasicMaterial map={tex} toneMapped={false} />
      </mesh>
    </group>
  );
}

function Ready({ onReady }: { onReady: () => void }) {
  useEffect(() => {
    onReady();
  }, [onReady]);
  return null;
}

function Bridge({ refs }: { refs: SceneRefs }) {
  const invalidate = useThree((s) => s.invalidate);
  useEffect(() => {
    refs.invalidate.current = invalidate;
    return () => {
      refs.invalidate.current = null;
    };
  }, [invalidate, refs]);
  return null;
}

export default function HeroScene({ refs, motion, active, onReady }: { refs: SceneRefs; motion: boolean; active: boolean; onReady: () => void }) {
  const shadow = useShadowTexture();
  const lowEnd = (navigator.hardwareConcurrency || 8) <= 4;
  const dpr = Math.min(window.devicePixelRatio || 1, lowEnd ? 1.25 : 1.75);

  useEffect(() => () => shadow.dispose(), [shadow]);

  return (
    <Canvas
      className="hero-canvas"
      dpr={dpr}
      frameloop={active ? 'always' : 'demand'}
      camera={{ position: [0, 0, 10], fov: 30 }}
      gl={{ antialias: !lowEnd, alpha: true, powerPreference: 'high-performance' }}
      aria-hidden="true"
    >
      <Bridge refs={refs} />
      <ambientLight intensity={0.9} />
      <directionalLight position={[3, 4, 6]} intensity={1.1} />
      <pointLight position={[-4, -2, 3]} intensity={6} color="#79D9F5" distance={12} />
      <Suspense fallback={null}>
        {PANELS.map((p) => (
          <Panel key={p.src} def={p} refs={refs} motion={motion} shadow={shadow} />
        ))}
        <Ready onReady={onReady} />
      </Suspense>
    </Canvas>
  );
}
