---
name: Academic Precision
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#434655'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#737686'
  outline-variant: '#c3c6d7'
  surface-tint: '#0053db'
  primary: '#004ac6'
  on-primary: '#ffffff'
  primary-container: '#2563eb'
  on-primary-container: '#eeefff'
  inverse-primary: '#b4c5ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#4d556b'
  on-tertiary: '#ffffff'
  tertiary-container: '#656d84'
  on-tertiary-container: '#eef0ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dbe1ff'
  primary-fixed-dim: '#b4c5ff'
  on-primary-fixed: '#00174b'
  on-primary-fixed-variant: '#003ea8'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#dae2fd'
  tertiary-fixed-dim: '#bec6e0'
  on-tertiary-fixed: '#131b2e'
  on-tertiary-fixed-variant: '#3f465c'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  headline-md:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  caption:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 48px
---

## Brand & Style
The brand personality for this design system is authoritative, dependable, and organized. It is designed for learners and educators who require a distraction-free environment that prioritizes information density without sacrificing clarity. 

The design style is **Corporate / Modern**, leaning heavily into functional minimalism. It utilizes a rigorous logical structure to reduce cognitive load. The aesthetic response should be one of "structured calm"—where the user feels in control of their learning path through clear visual hierarchies and a systematic interface.

## Colors
The palette is anchored by a "Trust Blue" primary color, used for primary actions, active navigation states, and progress indicators. 

- **Primary (#2563EB):** Logic and reliability. Used for buttons and key focus states.
- **Secondary (#64748B):** A muted slate for supporting text and icons.
- **Backgrounds:** A tiered system of White (#FFFFFF) for cards and main content areas, against a Light Gray (#F8FAFC) canvas to provide subtle contrast.
- **Semantic Palette:** High-saturation tokens for attendance tracking: Emerald for presence, Amber for permits/sick leave, and Rose for absence.

## Typography
The system uses **Inter** exclusively to leverage its exceptional legibility and systematic weight distribution. 

The type scale is optimized for reading long-form educational content and navigating complex data grids. Headlines use tighter letter-spacing and heavier weights to anchor sections. Labels use an uppercase transformation with slight tracking to differentiate them from body text in dense UI environments. On mobile devices, large headlines scale down to prevent excessive line-wrapping in card headers.

## Layout & Spacing
This design system utilizes a **12-column fluid grid** for desktop and a **4-column grid** for mobile. 

The spacing rhythm is based on a **4px baseline**. Component internal padding typically uses `16px (md)` or `24px (lg)` to ensure touch targets are accessible and content feels airy. 
- **The Schedule Grid:** Should follow a strict modular layout where time-slots are rows and days are columns. On mobile, this reflows into a vertical "List-Agenda" view.
- **Margins:** Desktop views maintain a generous 48px outer margin to keep the educational content centered and focused.

## Elevation & Depth
Depth is created using **Tonal Layers** and extremely soft, functional shadows. 

1.  **Level 0 (Base):** The #F8FAFC background.
2.  **Level 1 (Cards/Containers):** White (#FFFFFF) surfaces with a 1px border (#E2E8F0) and no shadow. This is the primary container for most content.
3.  **Level 2 (Interaction):** When a card or element is hovered, apply a 4px blur, 10% opacity black shadow with a 2px Y-offset.
4.  **Level 3 (Modals/Popovers):** Use a 12px blur, 15% opacity shadow to separate critical overlays from the main workspace.

## Shapes
The shape language is **Rounded**, using an 8px (0.5rem) base radius. This strikes a balance between the precision of a professional tool and the approachability of a learning environment. 

- **Small elements (Checkboxes):** 4px radius.
- **Standard elements (Buttons, Inputs, Cards):** 8px radius.
- **Large elements (Modals, Feature Banners):** 12px or 16px radius.
- **Status Badges:** Use a full pill-shape (999px) to distinguish them from interactive buttons.

## Components
- **Buttons:** Primary buttons are solid Blue (#2563EB) with white text. Secondary buttons use a light gray ghost style with a subtle border.
- **Attendance Badges:** Small, pill-shaped indicators.
    - *Present:* Light green background with dark green text.
    - *Sick/Permit:* Light amber background with dark amber text.
    - *Absent:* Light red background with dark red text.
- **Cards:** White background, 8px corner radius, 1px light gray border. Cards should have a clear "Header" section for titles and "Footer" for metadata or actions.
- **Input Fields:** Use a 1px border that shifts to the Primary Blue on focus. Labels sit directly above the field in `label-md` style.
- **Schedule Items:** Block-level elements within the grid that use subtle left-border color accents to denote subject categories.
- **Progress Bars:** Thin, 4px height bars using the Primary Blue for completion and a light gray for the track.