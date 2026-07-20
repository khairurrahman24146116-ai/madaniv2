---
name: Academic Clarity
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
  on-surface-variant: '#45474c'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#75777d'
  outline-variant: '#c5c6cd'
  surface-tint: '#545f73'
  primary: '#091426'
  on-primary: '#ffffff'
  primary-container: '#1e293b'
  on-primary-container: '#8590a6'
  inverse-primary: '#bcc7de'
  secondary: '#0058be'
  on-secondary: '#ffffff'
  secondary-container: '#2170e4'
  on-secondary-container: '#fefcff'
  tertiary: '#00190e'
  on-tertiary: '#ffffff'
  tertiary-container: '#00301e'
  on-tertiary-container: '#00a472'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d8e3fb'
  primary-fixed-dim: '#bcc7de'
  on-primary-fixed: '#111c2d'
  on-primary-fixed-variant: '#3c475a'
  secondary-fixed: '#d8e2ff'
  secondary-fixed-dim: '#adc6ff'
  on-secondary-fixed: '#001a42'
  on-secondary-fixed-variant: '#004395'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
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
  container-margin: 24px
  gutter: 16px
---

## Brand & Style

The design system is engineered for the high-cognitive-load environment of modern education. It balances professional authority with a welcoming, accessible atmosphere. The target audience—K-12 and higher education teachers—requires an interface that prioritizes data density without sacrificing legibility or emotional calm.

The style is **Corporate Modern** with a focus on functional clarity. It utilizes heavy whitespace to reduce visual noise and a structured information hierarchy to help teachers quickly transition between administrative tasks and student engagement. The aesthetic response is one of reliability, organization, and quiet encouragement.

## Colors

This design system utilizes a high-contrast palette to ensure accessibility and professional tone.
- **Primary (Navy Blue):** Used for navigation, headers, and core brand elements to establish trust and authority.
- **Secondary (Soft Blue):** Reserved for primary actions, links, and selection states, providing a clear path for user interaction.
- **Accent (Emerald Green):** Dedicated to positive indicators, progress completion, and success states to provide encouraging feedback.
- **Background (Light Gray/White):** A cool-toned neutral base that prevents eye strain during long grading sessions.
- **Semantic Colors:** Use standard reds (#EF4444) for alerts/late assignments and ambers (#F59E0B) for pending tasks.

## Typography

Inter is chosen for its exceptional legibility in data-heavy environments. The system uses a disciplined type scale to differentiate between administrative content and instructional data.

- **Headlines:** Use Bold or Semi-Bold weights with slight negative letter-spacing to appear more compact and authoritative.
- **Body Text:** Standardized at 16px for optimal reading of student feedback and reports. 
- **Labels:** Used for metadata, table headers, and form captions. Use Medium or Semi-Bold weights to ensure they are distinct from input text.
- **Numerical Data:** Tabular lining should be enabled where possible for alignment in gradebooks and attendance rosters.

## Layout & Spacing

The design system employs a **Fluid Grid** model with a 12-column structure for desktop. 

- **Layout Structure:** A persistent sidebar (240px-280px) houses primary navigation, while the main content area utilizes dynamic scaling.
- **Rhythm:** An 8px linear scale (4px, 8px, 16px, 24px, 32px, 48px, 64px) governs all padding and margins. 
- **Mobile Adaptivity:** On mobile devices, the 12-column grid collapses to a single column with 16px side margins. The sidebar transitions to a bottom navigation bar or a hidden drawer menu.
- **Data Density:** Use "Medium" density for general dashboards and "High" density (8px spacing) for gradebooks and student lists.

## Elevation & Depth

To maintain a modern, clean appearance, depth is conveyed through **Tonal Layers** and **Ambient Shadows**.

- **Level 0 (Background):** #F8FAFC. The canvas for all content.
- **Level 1 (Cards/Surface):** White (#FFFFFF) with a very soft, diffused shadow (0px 4px 6px -1px rgba(0, 0, 0, 0.05)). Used for primary dashboard widgets and content blocks.
- **Level 2 (Hover/Active):** Slightly deeper shadow (0px 10px 15px -3px rgba(0, 0, 0, 0.1)) to indicate interactivity.
- **Dividers:** 1px borders using #E2E8F0 are preferred over shadows for internal partitioning within cards to keep the UI flat and organized.

## Shapes

The shape language is defined by "Rounded" parameters (8px - 16px). This approach softens the professional Navy Blue palette, making the interface feel more approachable for an educational setting.

- **Standard Components:** Buttons, input fields, and small UI elements use 8px (0.5rem) corner radii.
- **Large Containers:** Dashboard cards and modal windows use 12px or 16px (1rem) corner radii to emphasize the "contained" and organized nature of the information.
- **Iconography:** Use icons with rounded terminals and soft corners to match the UI's roundedness.

## Components

- **Buttons:** Primary buttons use #3B82F6 with white text and 8px rounded corners. Secondary buttons use a subtle gray outline or ghost style.
- **Cards:** White background, 12px or 16px border-radius, and a subtle Level 1 shadow. Cards should always have a clear title in `headline-sm`.
- **Input Fields:** 8px border-radius with a 1px border (#D1D5DB). On focus, the border shifts to #3B82F6 with a soft blue outer glow.
- **Chips/Badges:** Used for student status (e.g., "Present", "Late"). Use high-contrast text on low-opacity backgrounds (e.g., Emerald Green text on a 10% opacity Emerald Green background).
- **Lists:** Use alternating row highlights or subtle dividers. Each list item should have a minimum touch target of 44px.
- **Progress Bars:** Use #10B981 for completed progress. Ensure the track color is a very light gray (#F1F5F9) for clear contrast.
- **Additional Elements:** Calendar widgets, gradebook tables, and "Assignment Ribbons" should follow the 8px spacing and rounded corner logic consistently.