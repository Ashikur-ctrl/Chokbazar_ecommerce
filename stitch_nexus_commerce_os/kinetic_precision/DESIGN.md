---
name: Kinetic Precision
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
  on-surface-variant: '#45464d'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#0051d5'
  on-secondary: '#ffffff'
  secondary-container: '#316bf3'
  on-secondary-container: '#fefcff'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#001f26'
  on-tertiary-container: '#0090a9'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#dbe1ff'
  secondary-fixed-dim: '#b4c5ff'
  on-secondary-fixed: '#00174b'
  on-secondary-fixed-variant: '#003ea8'
  tertiary-fixed: '#acedff'
  tertiary-fixed-dim: '#4cd7f6'
  on-tertiary-fixed: '#001f26'
  on-tertiary-fixed-variant: '#004e5c'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
  success: '#22C55E'
  surface-dark: '#020617'
  border-subtle: rgba(15, 23, 42, 0.08)
  glass-bg: rgba(255, 255, 255, 0.7)
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 42px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
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
  label-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  mono-label:
    fontFamily: JetBrains Mono
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
  unit: 8px
  container-max: 1440px
  gutter: 24px
  margin-mobile: 16px
  section-gap: 80px
---

## Brand & Style

The brand personality is defined by "Invisible Excellence"—a state where the interface is so intuitive and high-performing that it recedes, allowing the user's data and AI insights to take center stage. It targets executive decision-makers and high-scale engineers who demand speed and clarity.

The design style merges **Minimalism** with **Glassmorphism**. It utilizes heavy whitespace to denote premium quality, a restricted but purposeful color palette for functional signaling, and subtle translucency to provide a sense of spatial depth. The aesthetic is rigorous, balanced, and uncompromisingly professional, drawing inspiration from high-end hardware and developer-centric productivity tools.

## Colors

The palette is anchored by deep slates and vibrant blues, signaling both stability and innovation. 

- **Primary (#0F172A):** Used for primary text, deep backgrounds in dark mode, and high-contrast UI elements.
- **Secondary (#2563EB):** The functional "Action Blue," used for primary calls to action, active states, and focus indicators.
- **Accent (#06B6D4):** Used sparingly for AI-related highlights and secondary data visualizations to provide a sense of "intelligence."
- **Background (#F8FAFC):** A cool, airy neutral that provides a clean canvas for the light mode experience.

**Color Modes:**
- **Light Mode:** Uses high-contrast typography against the neutral background with soft, light-gray borders.
- **Dark Mode:** Transitions the primary background to `#020617`, using subtle gradients and glass effects to maintain layer separation without relying on pure black.

## Typography

This design system utilizes **Inter** for its systematic, utilitarian, and highly legible characteristics. The typographic scale is designed for density and hierarchy in complex dashboards.

Headlines use tighter letter spacing and heavier weights to create a "locked-in" feel. Body copy is optimized for long-form reading with generous line heights. A secondary monospace font is introduced for data-heavy labels and AI-generated IDs, adding a technical layer to the premium aesthetic.

## Layout & Spacing

The layout follows a **Fluid Grid** model with a max-width container for desktop viewing. A consistent 8px base unit (the "Step") governs all padding and margins to ensure mathematical harmony.

- **Desktop (1440px+):** 12-column grid, 24px gutters, and 48px+ outer margins. Use large section gaps (80px - 120px) to allow the interface to breathe.
- **Tablet (768px - 1024px):** 8-column grid with reduced gutters (16px).
- **Mobile (Under 768px):** 4-column grid with 16px side margins. Content should reflow vertically, with side-by-side elements stacking into single columns.

Alignment should be "flush-left" for maximum readability, with content grouped into logical "islands" separated by whitespace rather than heavy lines.

## Elevation & Depth

Hierarchy is achieved through a combination of **Tonal Layers** and **Linear-style shadows**. 

1.  **Base Layer:** Solid background (Light: #F8FAFC, Dark: #020617).
2.  **Surface Layer:** Background color with a subtle 1px border (`border-subtle`).
3.  **Raised Layer (Cards/Modals):** Features a "Linear Shadow"—a multi-layered shadow stack (e.g., 0 1px 2px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.05)).
4.  **Glass Layer:** Used for floating navigation and command palettes. Apply a 12px backdrop-blur and semi-transparent background (`glass-bg`).

The goal is to create a "shallow depth of field" where elements feel like they are floating just millimeters above the surface.

## Shapes

The shape language is "Rounded," utilizing a 0.5rem (8px) base radius. This provides a approachable yet professional feel that mimics modern hardware corners (like an iPhone or MacBook).

- **Standard Elements (Buttons, Inputs):** 8px (0.5rem).
- **Large Elements (Cards, Containers):** 16px (1rem).
- **Interactive States:** On hover, certain interactive elements may increase their visual presence through subtle scale-ups (1.02x) rather than changing their corner radius.

## Components

### Buttons
Primary buttons use the Secondary Blue (#2563EB) with white text. Secondary buttons use a ghost style with a subtle border and no background until hover. Interaction should be snappy, with a 150ms transition duration.

### Command Palette
The flagship input component. A centered, large-scale modal with a glassmorphic background, featuring a search icon, `mono-label` shortcuts (e.g., "⌘ K"), and a list of actionable results with high-contrast active states.

### Card Systems
Cards are border-dominant rather than shadow-dominant. Use a `1px` solid border (`border-subtle`). For AI-specific cards, use a very subtle inner glow or a top-border highlight in the Accent color (#06B6D4).

### Data Visualization
Charts should use clean, thin lines. The color palette for series should progress from Primary Blue to Accent Cyan to Success Green. Avoid grid lines where possible, using hover tooltips for specific data point extraction.

### Input Fields
Inputs should have a minimal footprint. In an inactive state, they should be simple underlines or very light boxes. On focus, the border should transition to Secondary Blue with a subtle outer glow (0 0 0 4px rgba(37, 99, 235, 0.1)).