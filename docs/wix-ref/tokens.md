# Design tokens — harvested from Wix Studio theme CSS

Source: theme stylesheet embedded in the rendered pages of
`https://soulbygirl.wixstudio.com/marinanews` (Wix Studio / Thunderbolt).
Extracted from the served CSS, not from a live-browser computed pass — values
below are the theme definitions the browser resolves against.

Scaling note: Wix Studio sizes display type with
`--theme-spx-ratio = var(--scaling-factor) / 1440` (spx units). At the design
viewport of 1440px, `N * spx-ratio` = `N px`. Each `font_X` class also carries
a `font-size: max(MIN, min(MAX, calc(N * ratio)))` clamp.

## Font families

| Role | Family | Fallback stack | Delivery |
|---|---|---|---|
| Display / all headings (`font_0`–`font_6`) | Arial Black | `'arial black', arial-w01-black, sans-serif` | Wix-hosted `arial-w01-black` woff |
| Body (`font_7`–`font_9`) | Avenir LT 35 Light | `avenir-lt-w01_35-light1475496, sans-serif` | Wix-hosted woff |
| Body alt / small (`font_1`, `font_10`) | DIN Next Light | `din-next-w01-light, sans-serif` | Wix-hosted woff |
| Also loaded (used in widgets/rich text) | Avenir LT 85 Heavy, Helvetica Neue 45 Light, Proxima Nova Reg, Rubik Light, Space Grotesk, Madefor (Wix UI) | — | — |
| Custom uploaded fonts | `wf_06b039e744b34faab84ef5728`, `wf_88119413f76a4700a45a5d8cf` | — | `static.wixstatic.com/ufonts/a9ff3b_*` |

## Type scale (theme `font_0` … `font_10`)

Format: `font_X` = size/line-height family — clamp — letter-spacing — default color.

| Token | Base (1440px) | Line height | Family | Clamp (min/max px) | Letter-spacing | Color |
|---|---|---|---|---|---|---|
| font_0 (hero) | 225spx | 0.85em | Arial Black | 60 / 240 | 0 | color_15 |
| font_1 | 16px | 1.4em | DIN Next Light | — | 0 | color_14 |
| font_2 (h1) | 97spx | 0.8em | Arial Black | 100 / 130 | −0.05em | color_15 |
| font_3 (h2) | 67spx | 1em | Arial Black | 50 / 90 | −0.05em | color_15 |
| font_4 (h3) | 37spx | 1em | Arial Black | 30 / 50 | −0.05em | color_15 |
| font_5 (h4) | 22spx | 1em | Arial Black | 20 / 30 | −0.05em | color_15 |
| font_6 (h5) | 16spx | 1.3em | Arial Black | 20 / 22 | 0 | color_15 |
| font_7 (large body) | 24spx | 1.5em | Avenir LT 35 Light | 22 / 32 | −0.05em | color_15 |
| font_8 (body) | 18px | 1.5em | Avenir LT 35 Light | — | 0 | color_15 |
| font_9 (small body) | 14px | 1.9em | Avenir LT 35 Light | — | 0 | color_15 |
| font_10 (micro) | 12px | 1.4em | DIN Next Light | — | 0 | color_14 |

## Colors (theme palette, `--color_X: R,G,B`)

Core brand slots (Wix convention: 11–15 = site palette light→dark accents):

| Token | RGB | Hex | Note |
|---|---|---|---|
| color_11 | 0,0,0 | `#000000` | primary dark / backgrounds |
| color_12 | 71,71,66 | `#474742` | dark warm gray |
| color_13 | 168,168,157 | `#A8A89D` | mid warm gray |
| color_14 | 227,227,211 | `#E3E3D3` | light warm sand (secondary text per font_1/font_10) |
| color_15 | 255,255,255 | `#FFFFFF` | white (default heading/body color) |

Base slots:

| Token | RGB | Hex |
|---|---|---|
| color_0 / color_1 / color_8 | 255,255,255 | `#FFFFFF` |
| color_2 | 0,0,0 | `#000000` |
| color_3 | 237,28,36 | `#ED1C24` |
| color_4 | 0,136,203 | `#0088CB` |
| color_5 | 255,203,5 | `#FFCB05` |
| color_6 / color_9 | 114,114,114 | `#727272` |
| color_7 / color_10 | 176,176,176 | `#B0B0B0` |

Extended slots 16–35 are Wix auto-generated tints of the palette (e.g.
color_16 `37,136,7`, color_17 `64,233,12`, color_18 `115,246,75`,
color_19 `164,249,139` — a green ramp) and slots up to 65 continue in ramps;
the site design reads as **black background + white Arial Black display type +
warm sand `#E3E3D3` secondary text**.

## Card / image ratios (from served image fill params)

| Context | Rendered fill sizes | Ratio |
|---|---|---|
| Blog list / category cards (thumbnail) | 333×250, 305×229, 334×250 | ≈ 4:3 landscape |
| Blog featured/large images | 1197×899, 1220×916, 481×360 | ≈ 4:3 landscape |
| Event-list card images | 147×196, 147×186 | ≈ 3:4 portrait |
| Instagram grid tiles | square crop in grid (source images mixed portrait) | 1:1 |

## Misc

- Design viewport: 1440px (`--scaling-factor / 1440`).
- Heading letter-spacing is consistently **−0.05em** (tight Arial Black).
- Hero (`font_0`) runs up to 240px with 0.85em line-height — oversized
  display type is a core signature of the layout.
- Favicon: Wix default (no custom favicon set on the test site).
- Logo: image `public/media/brand/logo.png`
  (`e628c7_af3905e3939a4848bb05be536887870b~mv2.png`).
