---
name: Academic Precision Dark
colors:
  surface: '#0b1326'
  surface-dim: '#0b1326'
  surface-bright: '#31394d'
  surface-container-lowest: '#060e20'
  surface-container-low: '#131b2e'
  surface-container: '#171f33'
  surface-container-high: '#222a3d'
  surface-container-highest: '#2d3449'
  on-surface: '#dae2fd'
  on-surface-variant: '#c1c7d3'
  inverse-surface: '#dae2fd'
  inverse-on-surface: '#283044'
  outline: '#8b919d'
  outline-variant: '#414751'
  surface-tint: '#a4c9ff'
  primary: '#a4c9ff'
  on-primary: '#00315d'
  primary-container: '#60a5fa'
  on-primary-container: '#003a6b'
  inverse-primary: '#0060ac'
  secondary: '#b9c8de'
  on-secondary: '#233143'
  secondary-container: '#39485a'
  on-secondary-container: '#a7b6cc'
  tertiary: '#bcc7de'
  on-tertiary: '#263143'
  tertiary-container: '#98a3ba'
  on-tertiary-container: '#2e394c'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#d4e3ff'
  primary-fixed-dim: '#a4c9ff'
  on-primary-fixed: '#001c39'
  on-primary-fixed-variant: '#004883'
  secondary-fixed: '#d4e4fa'
  secondary-fixed-dim: '#b9c8de'
  on-secondary-fixed: '#0d1c2d'
  on-secondary-fixed-variant: '#39485a'
  tertiary-fixed: '#d8e3fb'
  tertiary-fixed-dim: '#bcc7de'
  on-tertiary-fixed: '#111c2d'
  on-tertiary-fixed-variant: '#3c475a'
  background: '#0b1326'
  on-background: '#dae2fd'
  surface-variant: '#2d3449'
typography:
  headline-xl:
    fontFamily: Source Serif 4
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Source Serif 4
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Source Serif 4
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
  body-md:
    fontFamily: Hanken Grotesk
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Hanken Grotesk
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-caps:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-tabular:
    fontFamily: JetBrains Mono
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 0.25rem
  sm: 0.5rem
  md: 1rem
  lg: 1.5rem
  xl: 2.5rem
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
---

## Brand & Style
The design system evolves into a focused, low-strain environment tailored for long-form research, data analysis, and deep work. The brand personality remains authoritative, intellectual, and meticulous, but shifts its emotional response toward "nocturnal focus" and "digital sanctuary." 

The design style utilizes **Modern Corporate** principles with a heavy emphasis on **Tonal Layering**. By using depth rather than light to signify hierarchy, the UI minimizes eye fatigue while maintaining the rigorous structure required for academic and professional applications. High-contrast elements are reserved strictly for primary actions, ensuring the user's attention is directed only where necessary.

## Colors
The palette is rooted in deep navy and charcoal to provide a stable, "ink-like" foundation. 
- **Primary:** A softened "Electric Blue" (#60A5FA) that provides high visibility without the harshness of a pure neon.
- **Surface Strategy:** We use a "Deep Navy" (#0F172A) for primary containers and an "Ink Black" (#020617) for the global background to create a sense of infinite canvas.
- **Functional Grays:** Text and borders utilize the Slate scale to maintain a cool, professional temperature, avoiding the muddy feel of pure neutral grays.

## Typography
The typography pairing balances traditional academic authority with modern technical precision.
- **Serif Headlines:** Source Serif 4 provides the "published" feel, rendered in high-contrast off-white to ensure maximum legibility against dark backgrounds.
- **Sans-Serif Body:** Hanken Grotesk is used for interface text and long-form reading, selected for its clean metrics and contemporary clarity.
- **Monospaced Accents:** JetBrains Mono is utilized for labels, metadata, and data tables, reinforcing the "precision" aspect of the design system.

## Layout & Spacing
The design system employs a **Fixed Grid** on desktop (1280px max-width) and a **Fluid Grid** on mobile. 
- **The 8px Rhythm:** All padding, margins, and component heights must be multiples of 4px, with 8px being the primary unit of separation.
- **Density:** Maintain a "Moderate" density. In dark mode, whitespace (or "darkspace") is critical to prevent the UI from feeling claustrophobic. 
- **Grid:** 12 columns for desktop, 4 columns for mobile. Gutters are fixed at 24px to ensure distinct separation of content modules.

## Elevation & Depth
In this dark mode environment, depth is expressed through **Tonal Layers** rather than shadows. 
- **Level 0 (Background):** #020617 - The furthest back layer.
- **Level 1 (Surface):** #0F172A - Default card and container color.
- **Level 2 (Elevated):** #1E293B - Used for hover states or modals.
- **Outlines:** Instead of heavy shadows, use 1px solid borders in #334155 (Slate 700). This provides "architectural" definition that is crisper than shadows in a dark UI.
- **Glow:** Subtle primary-colored outer glows (opacity 10-15%) can be used for active state focus indicators.

## Shapes
The shape language is "Soft Professional." 
- **Standard Radius:** 4px (0.25rem) for functional elements like inputs and buttons to maintain a precise, structured feel.
- **Container Radius:** 8px (0.5rem) for cards and sections. 
- **Strictness:** Avoid pill shapes or circular buttons except for icon-only utility toggles. Rectilinear forms reinforce the academic and data-driven narrative.

## Components
- **Buttons:** 
  - *Primary:* Filled with #60A5FA, text in #020617 (Bold). 
  - *Secondary:* Ghost style with #334155 border and #F8FAFC text.
- **Inputs:** Background #020617, border #334155. Focus state uses a #60A5FA 1px border.
- **Chips:** Monospaced JetBrains Mono text. Background #1E293B, no border.
- **Cards:** #0F172A background with a #1E293B top-border (2px) to add a "ledger" style accent.
- **Lists:** Separated by #1E293B hair-lines (1px). Hover states shift the background to #1E293B.
- **Data Tables:** Header row uses #0F172A with `label-caps` typography. Row zebra-striping is discouraged; use subtle borders instead.