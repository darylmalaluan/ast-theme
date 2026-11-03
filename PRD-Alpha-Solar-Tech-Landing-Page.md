# PRD — Alpha Solar Tech Landing Page (custom WordPress theme)

**Status:** Ready to build. **Do not commit or push without the owner's direct go-ahead.**
**Owner:** Nico (design, via Desi) · **Builder:** Stack · **Prepared:** 3 Nov 2026 (Asia/Manila)

---

## 0. Where the design is (read this first)

| What | Link |
|---|---|
| **Figma file (source of truth)** | **https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=69-2** |
| Page to build | Page **R05** (`69:2`), the latest revision |
| Frame to build | **Landing Page** `72:283`, 1920 × 4960, desktop only: https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-283 |
| Older revisions (ignore) | Pages R04 `83:2`, R03 `51:447`, R02 `23:6`, R01 `0:1` hold earlier options. **Do not build from them.** |

- fileKey: `1s15xOE90n4kCVmihkOB2V`. Per-section node links are in §4.
- **What the file contains:** one 1920px desktop frame. There is **no** mobile, tablet or 1440 frame, no light/dark variants, no components or variants, no local variables, no text/colour/effect styles, no layout grid and no prototype interactions. Everything is loose, absolutely positioned layers (no auto layout). All values below were measured directly from the layers.
- **Exported assets** are in the `alpha-solar-assets/` folder next to this PRD on the Desktop (`/Users/nicocardenas/Desktop/alpha-solar-assets/`) (see §7).

---

## 1. Overview and goal

This is a single-page investor-facing landing page for **Alpha Solar Technologies (AST)** and its **ArchiSolar** BIPV technology. It does three things: announces the refreshed identity and new website (coming 1 February 2027), explains the licensing business model, and collects investment enquiries through a contact form.

**Goal:** build a faithful, accessible, fast **custom WordPress theme** that reproduces the R05 frame with pixel accuracy at 1920px. It must scale cleanly to 1440px and down to mobile, and the client must be able to edit all copy and images in wp-admin.

## 2. Scope and non-goals

**In scope**
- A custom classic theme `alpha-solar` (PHP templates, no block theme). Section list in §4.
- Editable content (ACF, §3.3), images via the Media Library, header CTA via `wp_nav_menu`.
- A **Contact Form 7** investment enquiry form, fully restyled to the design (§9).
- Responsive behaviour for 1920, 1440, 1024, 768 and 390 (proposed, §10), subtle scroll-reveal (§11), WCAG 2.2 AA (§12), performance/SEO basics (§13).

**Non-goals**
- No page builders (Elementor, WPBakery, Divi and so on) and no block-pattern layout for this page.
- No other pages beyond the front page and a styled default `page.php` (needed for the Privacy Policy link target).
- No blog, no CPTs, no e-commerce, no CRM integration (unless decided in §15), no multilingual.
- Do not redesign. Where this PRD proposes something the design doesn't show (mobile, states, a11y fixes), it's marked **[PROPOSED]**.

## 3. Theme architecture

### 3.1 Structure
```
wp-content/themes/alpha-solar/
├── style.css                 # theme header only (Theme Name: Alpha Solar, Version: 1.0.0, Text Domain: alpha-solar)
├── functions.php             # require inc/*
├── front-page.php            # get_header(); loops template-parts in order; get_footer();
├── page.php                  # simple default page (Privacy Policy etc.)
├── index.php                 # fallback
├── header.php / footer.php
├── inc/
│   ├── setup.php             # theme supports, menus, image sizes
│   ├── enqueue.php           # styles/scripts, fonts preload, CF7 asset control
│   ├── acf-fields.php        # acf_add_local_field_group() (fields in code, versioned)
│   ├── helpers.php           # ast_highlight() (see 3.4), ast_img()
│   └── cf7.php               # CF7 filters (see §9)
├── template-parts/
│   ├── site-header.php
│   ├── section-hero.php
│   ├── section-announcement.php
│   ├── section-business-model.php
│   ├── section-licensing.php
│   ├── section-contact.php
│   └── site-footer.php
├── assets/
│   ├── css/main.css          # (built from src if Stack uses a build step; plain CSS is fine)
│   ├── js/main.js            # reveal + carousel, vanilla, deferred
│   ├── img/                  # SVG logo/icons + decorative textures (hex, bg texture)
│   └── fonts/                # self-hosted Montserrat woff2 (400, 600, 700, 800)
└── acf-json/                 # optional, if Stack prefers JSON sync over PHP field groups
```

### 3.2 WordPress specifics
- `add_theme_support`: `title-tag`, `post-thumbnails`, `html5` (search-form, gallery, caption, style, script), `responsive-embeds`, `custom-logo` (logo falls back to `assets/img/logo-ast.svg`).
- `register_nav_menus( [ 'primary' => 'Header CTA', 'footer' => 'Footer links' ] )`. The header renders `wp_nav_menu( theme_location 'primary', depth 1, container false )`, and the menu item (**Contact Us → `#contact`**) gets the pill-button style. Fallback: if no menu is assigned, render a hard-coded "Contact Us" link to `#contact`.
- Enqueue everything through `wp_enqueue_style` / `wp_enqueue_script` (`strategy => defer`, `in_footer => true`), versioned with the theme version. No CDN fonts; self-host Montserrat and `preload` the 700 and 800 woff2 files.
- Images: everything content-like comes from the Media Library via ACF image fields (return ID) and is rendered with `wp_get_attachment_image()` so `srcset`/`sizes` work. Register sizes: `ast-band` 1559×435 crop, `ast-band-2x` 3118×870, `ast-feature` 771×533, `ast-slide` 1372×435 crop, `ast-hero` 1920×1234.
- Decorative textures (hex pattern, grayscale texture, glow) are theme assets in `assets/img/` and CSS, not editable.
- Front page: Settings → Reading → static page "Home" using `front-page.php`. The Privacy Policy button uses `get_privacy_policy_url()`.

### 3.3 Content editing — **Decision: ACF**
Recommend **Advanced Custom Fields**, with field groups registered in code (`acf_add_local_field_group`) on the front page (location: page type = front page). This matches the approach Stack already uses on Sensoriium.
- The licensing carousel needs repeatable slides. With **ACF Pro**, use a Repeater or Gallery. With **ACF free**, use 3 fixed image fields (`licensing_slide_1..3`) and hide any that are empty. **→ Open question Q2: is an ACF Pro licence available?** Build with the free fallback if unknown.
- No Gutenberg content on the front page. Remove the editor (`remove_post_type_support` for the front page ID, or hide it via the ACF field-group setting `hide_on_screen`).

### 3.4 Highlighted words in headings
Several headings colour part of the text (yellow or white). Editors mark the highlight with `<em>` in a plain text field, for example `Built to be <em>seen</em>, designed to <em>perform</em>.`. `ast_highlight()` runs `wp_kses( $str, [ 'em' => [], 'br' => [] ] )`, and CSS styles `em` as `font-style: normal; color: var(--hl)`, where each heading sets its own `--hl`. Copy is stored in **sentence case** and uppercased with CSS (`text-transform: uppercase`), as Figma does.

---

## 4. Sections, in order (with verbatim copy and node links)

Coordinates are **x, y, w × h in px inside the 1920-wide frame** (frame origin = top-left). Node link pattern: `https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=X-Y`.

| # | Section | Vertical band (y) | Height | Background | Key node links |
|---|---|---|---|---|---|
| 1 | Header (overlays hero) | 106–160 | — | transparent | [logo 72-317](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-317), [Contact Us 72-301](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-301) |
| 2 | Hero | 0–1179 | 1179 | photo + glow + hex | [heading 72-343](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-343), [bg photo 72-287](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-287), [building 72-300](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-300) |
| 2b | Hero image band (overlaps 2 → 3) | 1067–1502 | 435 | image | [72-403](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-403) |
| 3 | Announcement | 1179–2037 | 858 | `#0A0A0A` + hex | [bg 72-303](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-303), [heading 72-348](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-348), [button 72-364](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-364) |
| 4 | Business model | 2037–2816 | 779 | white + 10% grayscale texture + hex | [heading 72-344](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-344), [body 72-346](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-346), [image 72-424](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-424) |
| 5 | Licensing model | 2816–3797 | 981 | `#FAC84F` + hex | [bg 72-351](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-351), [heading 72-412](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-412), [slide 72-406](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-406) |
| 6 | Contact / investment enquiry | 3797–4819 | 1022 | `#0A0A0A` + hex | [bg 72-304](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-304), [heading 72-349](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-349), [fields 72-392](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-392) |
| 7 | Footer | 4819–4960 | 141 | `#000000` | [bg 72-400](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-400), [text 72-401](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-401) |

Hidden layers to **ignore** (not built): `72:286` solar-panel image, `72:289` blue gradient rect, `72:291` orange glow ellipse, and the hidden first fill on `72:300` / the hidden gradient fill on `72:287`.

### 4.1 Header — overlays the hero
| Property | Value |
|---|---|
| Logo | [72-317](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-317): vector "AST / ALPHA SOLAR TECH", fill `#FFFFFF`/`#FDFDFD`, **170 × 53.79** at x179, y106. File: `logo-ast.svg`. Link to home (`home_url('/')`), `aria-label="Alpha Solar Technologies — home"`. |
| CTA "Contact Us" | [72-301](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-301): pill **165.38 × 47** at x1577, y113 (right edge 1742), fill `#FAC84F`, radius 100 (fully rounded), no border. Text centred, 42px horizontal padding, 16px top. |
| CTA text | **Suisse Int'l Book (450), 16px, line height 15px, letter spacing −0.2px, sentence case, `#000000`**. ⚠ Suisse Int'l is a commercial font and is the only use of it on the page → **Q3** (licence, or swap to Montserrat 500). |
| Header rule (decorative) | [Vector 1 72-292](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-292): 1px stroke, `#FFFFFF` at 46%. Path: horizontal from (178.5, 218) to x1653.5, then a curve of ~88px radius into a vertical line at x1742 running down to y1175. It's drawn **under** the building cut-out (`72:300`), so the building covers it on the right. |
| Vertical rule | [Vector 2 72-402](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-402): 1px `#FFFFFF` at 78%, x1742, y407 → 1502. It sits **above** everything (it shows over the building and the image band). |
| Layout | Logo left / CTA right, vertically centred on y≈133 (logo centre 132.9, button centre 136.5; keep the Figma offsets). Not sticky (Figma shows no sticky state) → **Q9**. |
| States [PROPOSED] | Hover: bg `#FFD36B` (yellow lightened ~8%), 150ms. Focus-visible: 3px outline `#FFFFFF`, 3px offset. Active: translateY(1px). |

### 4.2 Hero
**Copy (verbatim)**
- Eyebrow ([72-350](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-350)): `ALPHA SOLAR TECHNOLOGIES`
- H1 ([72-343](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-343)): `Built to be seen, designed to perform.`. "seen" and "perform" are yellow; the rest, including ", designed to " and the final ".", is white. ACF value: `Built to be <em>seen</em>, designed to <em>perform</em>.`
- Body ([72-345](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-345)), with a hard line break after the first sentence:
  > ArchiSolar brings architectural design and solar energy together.
  > Developed by Alpha Solar Technologies, our technology is designed to bring high-resolution colour, imagery and finishes to building-integrated photovoltaics (BIPV), while preserving as much solar energy output as possible. Our ambition is to give architects and building owners greater freedom to create the look they want, with solar performance at the heart of the design.
- Scroll-down button ([72-573](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-573)): icon only.

| Element | Position / size | Style |
|---|---|---|
| Section | 0–1179, full width | Layers bottom→top: (1) bg photo `72:287` (2969×1248 at x−829, y−14, cropped to the frame; export `hero-bg-photo-1920x1234.png`, a desaturated grey sky with the building); (2) **glow** `72:290`, an ellipse 1483×1162 at x656, y91, radial gradient `#FAC84F` 100% → 0% centre-to-edge, fill opacity 64%; (3) hex textures `72:293` (circle 1048 at x1330, y47, group opacity 12%) and `72:297` (circle 1048 at x612, y104, opacity 16%), each the hex image (`hex-pattern-951x951.png`, ~950.6px, inset ~80/49px) masked by a radial white→transparent circle; (4) building cut-out `72:300` (transparent PNG `hero-building-foreground-1920x1234.png`); (5) text. The dark section (§4.3) starts at y1179 and covers the lower edge of the photo. |
| Eyebrow | x179, y342, w593, h18 | Montserrat ExtraBold 800, 16px, LH 120% (19.2px), LS −0.2px, as typed (already caps), `#D3B69C` |
| H1 | x179, y387, w670, h200 (3 lines: "BUILT TO BE / SEEN, DESIGNED / TO PERFORM.") | Montserrat Bold 700, **72px, LH 95% (68.4px), LS −0.19px**, uppercase, `#FFFFFF`, highlight `#FAC84F` |
| Body | x179, y628, w643, h258 | Montserrat Regular 400, 18px, LH 150% (27px), LS −0.2px, `#FFFFFF` |
| Gaps | eyebrow→H1 27px (342+18→387); H1 box→body 41px; body→scroll button ≈−13px (button y873 overlaps the 258px text box; visually ~18px below the last line, so measure against the rendered last line) | |
| Scroll button | 60×60 circle at x179, y873, fill `#1E1E1E`; chevron-down stroke `#FAC84F` 4.29px, round caps/joins, path 27.1×13.6 centred. File `icon-scroll-down.svg`. | `<a href="#announcement" aria-label="Scroll to next section">`. Hover [PROPOSED]: bg `#000`, chevron translateY(2px). |

**Spacing within the 1920 frame:** content left edge 179; right edge 1742 (outer content width 1563). Text column ≈ 5 of 12 columns (see §6.4).

### 4.3 Hero image band (straddles hero → announcement)
| Property | Value |
|---|---|
| Node | [72-403](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-403), a mask group |
| Box | **x183, y1067, 1559 × 435**, aspect **3.584 : 1**, no radius, no shadow |
| Image | "empty floor and modern architectural passageway" (glass façade). In Figma it's rotated 180°, exposure −0.15 and contrast +0.57, all baked into the export `hero-image-band-1559x435.png`. Use `object-fit: cover; object-position: center`. Editable (ACF `hero_band_image`). |
| Overlap | 112px over the hero (1067–1179), 323px into the dark section (1179–1502). Implement it as part of the announcement section with a `margin-top: -112px` (or position it absolutely across the boundary), with `z-index` above both section backgrounds. |
| Rules around it | **Line 3** [72-410](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-410): 1px `#FFF` at 78% along the top edge, y1067, x183→1742. **Line 2** [72-409](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-409): 0.5px `#FFF` at 50%, y1501, x184→1920 (it runs to the right viewport edge). **Line 1** [72-411](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-411): 0.5px `#FFF` at 50%, vertical at x184, y1068→2037 (down the left of the dark section). Vector 2 (x1742, to y1502) closes the right side. Render the 0.5px lines as 1px at 50% × 0.5 ≈ `rgba(255,255,255,.25)` on 1x screens, or use `0.5px` on HiDPI. |

### 4.4 Announcement (dark)
**Copy:** H2 ([72-348](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-348)) `Our refreshed identity and new website is coming: 1 February 2027.`. "refreshed identity" and "1 February 2027" are yellow; the rest is white. Button: `ENQUIRE ABOUT INVESTMENT`.

| Element | Position / size | Style |
|---|---|---|
| Section bg | y1179–2037 (858), `#0A0A0A` | Hex textures at **4%**: `72:305` (circle 1296 at x−506, y741) and `72:308` (circle 1296 at x1239, y897), hex image ~1175.5px, radial mask. |
| H2 | x555, y1609, w809, centred text, 3 lines | Montserrat ExtraBold 800, **40px, LH 120% (48px), LS −0.2px**, uppercase, `#FFFFFF`, highlight `#FAC84F`. Figma breaks "…1 FEBRUARY / 2027." See the orphan rule in §6.5. |
| Gap | image band bottom (1502) → H2 107px; H2 box bottom (1723) → button 122px; button bottom (1911) → section end 126px | |
| Button (primary) | [72-364](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-364): **360 × 66** at x780 (centred: 780+180 = 960), y1845 | See "Primary pill button" in §5. Link → `#contact`. |

### 4.5 Business model (light)
**Copy**
- H2 ([72-344](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-344)): `Technology with global potential. A business model built around its use.`. The first sentence plus its trailing space is `#1E1E1E`; "A business model built around its use." is `#D8A01A` (see the contrast fix in §12).
- Body ([72-346](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-346)), two paragraphs:
  > Alpha Solar Technologies is developing ArchiSolar for commercial licensing, with the ambition to bring high-resolution architectural finishes to building-integrated solar worldwide. Our proposed commercial model combines licence fees, royalties and usage-based fees, linking potential revenue to the adoption and ongoing use of ArchiSolar technology.
  >
  > For investors, the opportunity extends beyond an individual panel or print: it is participation in the development and commercialisation of technology intended for use across international BIPV markets. Reach out if you have any questions.

| Element | Position / size | Style |
|---|---|---|
| Section bg | y2037–2816 (779): frame white `#FFFFFF` + texture `72:288` (grayscale passageway image, 10% opacity, saturation −1, 1902×934 at x7, y2037; it runs under the yellow section, which covers it). Export `business-model-bg-texture-1902x934.png` (opacity already baked in, so use it at 100%). | Hex textures at 5%: `72:383` (circle 508 at x41, y1878), `72:386` (492 at x618, y1870), `72:389` (496 at x1150, y1878), hex ~723.8px. These sit mostly in the dark section above; Figma draws them above the dark bg, so they only show faintly. |
| H2 | x179, y2171, w667, h200 (4 lines; "USE." ends alone in Figma) | Montserrat ExtraBold 800, 40px, LH 120% (48px), LS −0.19px, uppercase |
| Body | x179, y2403, w611, h357, 2 paragraphs with one empty line between (27px paragraph gap) | Montserrat Regular 400, 18px, LH 150%, LS −0.2px, `#1E1E1E` |
| Gaps | section top → H2 134px; H2 box → body 32px; image top y2160 (11px above H2 top) | |
| Image | [72-424](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-424): **x971, y2160, 771 × 533**, aspect **1.447 : 1**, no radius. Right edge 1742, aligned with the outer container. Gap between the text column's right (846) and the image is 125px. File `business-model-image-771x533.png` (façade, teal tones). | `object-fit: cover`. ACF `business_image`. |
| Vertical alignment | Text block top-aligned with the image (2160 vs 2171) | |

### 4.6 Licensing model (yellow) with carousel
**Copy**
- H2 ([72-412](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-412)): `A licensing model with revenue tied to every print`. "A licensing model with " is `#1E1E1E`; "revenue tied to every print" is `#FFFFFF` in Figma (**fails contrast**, §12).
- Body ([72-421](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-421)): `AST is preparing for a proposed NSX listing as we develop ArchiSolar for commercial licensing to glass manufacturers and printing technology companies.`
- Button ([72-413](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-413)): `REQUEST INVESTMENT INFORMATION`

| Element | Position / size | Style |
|---|---|---|
| Section bg | y2816–3797 (981), `#FAC84F` | Hex texture `72:352`: group 52% opacity, elliptical radial mask 1296×997 at x−533, y2789, hex image at 33% with exposure −0.73 (darker hexes). Export `licensing-hex-texture-763x997.png` (visible part, effects baked) and place it at left 0, top −27px relative to the section. |
| H2 | x585, y2941, w750, centred, 2 lines ("A LICENSING MODEL WITH / REVENUE TIED TO EVERY PRINT") | Montserrat ExtraBold 800, 40px, LH 120%, LS −0.2px, uppercase |
| Slide viewport | [72-406](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-406): **x274, y3098, 1372 × 435**, aspect **3.154 : 1**, no radius. Slide 1 file `licensing-slide-1-1372x435.png` (towers + sky). | Inner container 273/274 → 1646/1647 (1374 wide, centred). |
| Prev arrow | [Group 9 72-361](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-361): 60×60 circle `#1E1E1E` at x149, y3286 (bounding box), white chevron-left stroke 4.29px | Gap to slide: 65px. Vertically centred on the slide (3286+30 = 3316 ≈ slide centre 3315.5). |
| Next arrow | [Group 8 72-358](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-358): 60×60 at x1712, y3286, white chevron-right | Mirror of prev (gap 66px). Use the same rotated `icon-scroll-down.svg` geometry with a white stroke. |
| Gaps | section top → H2 125px; H2 box bottom (3055) → slide 43px; slide bottom (3533) → body/button 57px; button bottom (3663) → section end 134px | |
| Body | x273, y3590, w632 | Montserrat 400, 18px, LH 150%, LS −0.2px, `#1E1E1E` |
| Button (dark) | **429 × 73** at x1217 (right edge 1646), y3590; top-aligned with body text | "Dark pill button" in §5. Link → `#contact` (**Q6** confirm the target). |

**Carousel behaviour** (the arrows imply multiple slides; only one image exists → **Q1**):
- Vanilla JS, `scroll-snap-type: x mandatory` track with buttons that scroll by one slide. No library.
- Markup: `<section aria-roledescription="carousel" aria-label="Licensing imagery">`, slides `role="group" aria-roledescription="slide" aria-label="1 of N"`, and buttons `<button aria-label="Previous slide">` / `"Next slide"` with `aria-controls`.
- No autoplay. If there's only 1 slide, hide both arrows. At the ends, disable the arrow (`aria-disabled="true"`, opacity .4), or loop → **Q1**.
- Swipe on touch comes free with scroll-snap. Arrow keys work while focus is in the carousel.
- Slide change: native smooth scroll (`behavior: smooth`, or `auto` under reduced motion).

### 4.7 Contact / investment enquiry (dark)
**Copy:** H2 ([72-349](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-349)) `Discuss investment with AST`. "Discuss investment " is `#FAC84F`; "with AST" is `#FFFFFF`. Intro ([72-347](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-347)): `Contact our team to discuss investment in ArchiSolar’s development and commercialisation.` (curly apostrophe). Fields and buttons are in §9.

| Element | Position / size | Style |
|---|---|---|
| Section | `id="contact"`, y3797–4819 (1022), `#0A0A0A` | Hex textures 4%: `72:311` (circle 1296 at x822, y3404) and `72:314` (1296 at x−218, y3826). |
| H2 | box x584, w750, y3906. The text is left-aligned in Figma but the box is placed so it **reads as centred** on screen. Build it **centred** (`text-align:center`), one line. | Montserrat ExtraBold 800, 40px, LH 120%, LS −0.2px, uppercase |
| Intro | box x543, w833, y3976, one line, visually centred → build centred | Montserrat 400, 18px, LH 150%, LS −0.2px, `#FFFFFF` |
| Gaps | section top → H2 109px; H2 top → intro 70px (≈22px below the 48px line); intro → first field 103px (3976 → 4079) | |
| Form container | x273 → 1647 (1374 wide, centred) | |
| Row 1 | Full name **679 × 69** at x273; Email **676 × 69** at x971; column gap 19px | |
| Row 2 | Company **1374 × 69** at y4169 (row gap 21px) | |
| Row 3 | Enquiry textarea **1374 × 322** at y4259 (gap 21px) | |
| Field style | fill `#FFFFFF`, radius **5px**, border none (Figma has a hidden 1px `#222222` inside stroke) | Placeholder Montserrat 400, 18px, LH normal (~22px), `#828282` in Figma → use `#757575` (§12). Inset: left 31px; vertical: inputs centre the text (top 24px), textarea top 31px. |
| Buttons row | y4633, h66; textarea bottom 4581 → 52px gap | Primary "ENQUIRE ABOUT INVESTMENT" 360×66 at x604 + secondary "Privacy policy" 239×66 at x999; gap 35px. ⚠ In Figma the pair spans 604–1238 (centre 921), **39px left of the page centre**. **Decision:** centre the pair on 960 (the buttons only, keeping the 35px gap). Called out in §14 as an allowed deviation. |
| Section end | buttons bottom 4699 → footer 4819: 120px | |

### 4.8 Footer
| Element | Value |
|---|---|
| Bar | y4819–4960, **141px** high, `#000000`, full width |
| Text | [72-401](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-401): `© Alpha Solar Technologies Limited` at x183, y4880 (61px from top, so 61px top / ~53px bottom with LH 27px). Montserrat 400, 18px, LH 150%, LS −0.2px, `#FFFFFF`. Editable (ACF `footer_text`). Don't add a year (not in the design) → Q8. |
| Footer menu | `wp_nav_menu('footer')` registered but not output unless items exist (the design has none). |

---

## 5. Component inventory (build as PHP partials and CSS classes)
Figma has no components, so these are derived from repeated layers.

| Component | Instances | Spec |
|---|---|---|
| **Primary pill button** `.btn--primary` | Announcement [72-365](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-365), form submit [72-373](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-373) | 360 × 66, bg `#FAC84F`, radius 100. Label Montserrat SemiBold 600, 16px, LH 15px, LS −0.2px, `#000000`, uppercase as typed, label box x+30 (w258, centred → padding-left 30px). Icon: 36px black circle with a 12×12 white ↗ arrow, at x+309, y+15 (15px right and vertical padding). File `icon-button-arrow.svg`. |
| **Dark pill button** `.btn--dark` | Licensing [72-414](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-414) | **429 × 73**, bg `#1E1E1E`, radius 100. Label 600 / 16px / LH 15px / −0.2px, `#FFFFFF`, label box x+31 (w320). Icon: 36px `#FAC84F` circle with a `#1E1E1E` ↗, at x+371, y+19 (22px right padding). ⚠ It's 73px high vs 66px for the primary; keep the Figma heights (→ Q7). |
| **Outline pill button** `.btn--outline` | Privacy policy [72-381](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-381) | 239 × 66, transparent, **2px inside border `#FAC84F`**, radius 100. Label "Privacy policy" uppercased via CSS, 600 / 16px / LH 15px / −0.2px, `#FFFFFF`, centred. No icon. |
| **Header CTA pill** `.btn--cta-sm` | Contact Us [72-301](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-301) | 165.38 × 47 (padding 16px 42px), `#FAC84F`, radius 100, Suisse Int'l Book 16px (Q3). |
| **Round icon button** `.btn-round` | Scroll-down, carousel prev/next | 60 × 60, `#1E1E1E`, chevron stroke 4.29px round, 27.1 × 13.6. Yellow chevron in the hero, white in the carousel. |
| **Hex texture** `.hex` | 11 instances | Absolutely positioned `div`, `background: url(hex-pattern-951x951.png) center/cover`, `mask-image: radial-gradient(closest-side, #000, transparent)`, opacity per instance (12%, 16%, 4%, 5%, 52%·33%). `aria-hidden`, `pointer-events:none`. |
| **Section heading** `.h2` | 4 | 800 / 40 / 120% / −0.2px / uppercase, with `em` highlight |
| **Text input / textarea** | 4 | §9 |

**Button states [PROPOSED, all buttons]:** hover bg darkens or lightens ~8% (`#FAC84F`→`#F3B82E`; `#1E1E1E`→`#000`; outline → bg `rgba(250,200,79,.12)`) and the icon circle nudges `translate(2px,-2px)`. Focus-visible: 3px outline in a contrasting colour (`#FFFFFF` on dark and yellow-on-dark, `#1E1E1E` on yellow), 3px offset. Active: translateY(1px). Disabled (submit while sending): opacity .6, `cursor: progress`. Transition 150ms ease-out, disabled under reduced motion.

## 6. Design tokens
### 6.1 Colours (as found; no Figma variables exist)
| Token | Hex | Use |
|---|---|---|
| `--c-yellow` | `#FAC84F` | Brand accent, primary buttons, highlights, licensing bg, outline border |
| `--c-gold` | `#D8A01A` | Highlight in the business-model H2 (fails on white → `--c-gold-aa`) |
| `--c-gold-aa` [PROPOSED] | `#8A6500` | AA-safe gold text on white (5.28:1) |
| `--c-sand` | `#D3B69C` | Hero eyebrow |
| `--c-ink` | `#1E1E1E` | Dark text on light/yellow, dark button, round buttons |
| `--c-black` | `#000000` | Footer bg, button label, icon circle |
| `--c-night` | `#0A0A0A` | Dark section bg |
| `--c-white` | `#FFFFFF` | Text on dark, fields |
| `--c-placeholder` | `#828282` (Figma) → `#757575` [PROPOSED] | Field placeholder |
| `--c-error` [PROPOSED] | `#FF8A80` on dark (8.67:1) | Field error text |
| `--c-success` [PROPOSED] | `#7EE2A8` on dark (12.57:1) | Form success message |
| Line white | `#FFFFFF` at 78% (1px) / 50% (0.5px) / 46% (1px) | Decorative rules |

### 6.2 Typography
| Style | Family / weight | Size | Line height | Letter spacing | Case |
|---|---|---|---|---|---|
| Display H1 | Montserrat 700 | 72px | 95% (68.4px) | −0.19px | uppercase |
| H2 | Montserrat 800 | 40px | 120% (48px) | −0.2px (−0.19 on business H2; use −0.2 everywhere) | uppercase |
| Eyebrow | Montserrat 800 | 16px | 120% | −0.2px | caps as typed |
| Body | Montserrat 400 | 18px | 150% (27px) | −0.2px | sentence |
| Button | Montserrat 600 | 16px | 15px | −0.2px | uppercase |
| Header CTA | Suisse Int'l Book 450 (Q3) | 16px | 15px | −0.2px | sentence |
| Field text / placeholder | Montserrat 400 | 18px | normal (~22px) | 0 | sentence |

Font loading: self-host Montserrat woff2 (latin subset) for 400/600/700/800, `font-display: swap`, preload 700 and 800. Fallback stack `"Montserrat", "Helvetica Neue", Arial, sans-serif`, with `size-adjust` tuned to cut CLS.

### 6.3 Radii, borders, effects
- Radius: pills 100px (use `9999px`); fields 5px; images 0.
- Borders: outline button 2px `#FAC84F` inside; no others visible.
- Shadows/blur: **none** in the file. Glows are radial gradients, not blur.
- Gradients: hero glow `radial-gradient(closest-side, rgba(250,200,79,.64), rgba(250,200,79,0))` on a 1483×1162 ellipse at (656, 91).

### 6.4 Grid and container [PROPOSED; no grid is defined in Figma]
- Outer container: content 179 → 1742 at 1920 → `max-width: 1564px; margin-inline: auto; padding-inline: clamp(20px, 5vw, 64px)`. At 1920 this gives exactly 178px margins.
- Inner container (licensing, form): 1374px max (273 → 1647).
- 12-column CSS grid, 32px gutter, column ≈ 101px at 1564. Spans: hero text ≈ cols 1–5 (670px wide in Figma, keep `max-width: 670px`); business text cols 1–5 (`max-width: 667px`), image cols 7–12 (771px; left 971 ≈ col 7 start 977). **Use the measured px max-widths as authoritative** and the grid for placement.
- Values are deliberately not rounded to a 4px scale so they match Figma. **Q10**: normalise to multiples of 4 (Nico's usual rule)?

### 6.5 Heading line breaks
Nico's standing rule is no orphan word alone on a heading line. Figma's "2027." (announcement) and "USE." (business H2) break as orphans. **Decision:** apply `text-wrap: balance` to H1/H2 (and `pretty` to body). This changes those two headings' breaks from Figma; the pixel check accepts it (§14).

### 6.6 Z-order (bottom → top, from the Figma layer order)
hero bg photo → business texture → hero glow → header rule (Vector 1) → hero hex ×2 → building cut-out → Contact Us → dark bg (announcement) → dark bg (contact) → dark-section hexes ×4 → logo → all text → yellow bg → licensing hex → carousel arrows → buttons → business hexes ×3 → form fields → footer → Vector 2 → image band → Lines 1–3 → licensing heading/button/body → business image → scroll button.
Practical CSS: each section is its own stacking context (`position:relative; isolation:isolate`), decorations at z 0, content at z 1, and the image band plus its rules at z 2 above the hero/announcement boundary. Vector 2 sits on top of the building and image band.

## 7. Assets (exported from Figma; box `/workspace/alpha-solar/assets/`, also delivered to `/Users/nicocardenas/Desktop/alpha-solar-assets/`)
| File | Size | Source node | Use / notes |
|---|---|---|---|
| `logo-ast.svg` | 170×53.79 | [72-318](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-318) | Header logo (white). Theme asset / custom-logo default. |
| `icon-scroll-down.svg` | 60×60 | [72-573](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-573) | Round button + chevron. Rotate ±90° and change the stroke colour for the carousel arrows. Better as inline SVG with `currentColor`. |
| `icon-button-arrow.svg` | 36×36 | [72-366](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-366) | ↗ icon in a circle. Inline it with `currentColor` for both colourways. |
| `hero-bg-photo-1920x1234.png` | 1920×1234 | [72-287](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-287) | Hero bg (cropped to the frame). Convert to AVIF/WebP plus a JPG fallback. Upload to the Media Library (ACF `hero_bg_image`). |
| `hero-building-foreground-1920x1234.png` | 1920×1234, transparent | [72-300](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-300) | Building cut-out above the glow/hex. WebP with alpha. ACF `hero_fg_image`. |
| `hero-image-band-1559x435.png` | 1559×435 | [72-403](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-403) | Image band (rotation and filters baked in). |
| `business-model-image-771x533.png` | 771×533 | [72-424](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-424) | Business image. |
| `licensing-slide-1-1372x435.png` | 1372×435 | [72-406](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-406) | Carousel slide 1. |
| `hex-pattern-951x951.png` | 951×951, transparent | [72-299](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-299) | Hex line pattern (light, exposure +1). Mask and opacity via CSS. |
| `licensing-hex-texture-763x997.png` | 763×997, transparent | [72-352](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-352) | Darker hex on yellow, mask and opacity baked in. |
| `business-model-bg-texture-1902x934.png` | 1902×934, 10% alpha baked | [72-288](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-288) | Grayscale texture behind the business section. |
| `reference-full-page-1920x4960.png` | 1920×4960 | [72-283](https://www.figma.com/design/1s15xOE90n4kCVmihkOB2V/Alpha-Solar-Tech-Landing-Page?node-id=72-283) | **Pixel-fidelity reference** for overlay tests. Not shipped in the theme. |

All exports are at 1× (the exports were capped at the node's on-canvas size). For 2× retina, re-export the photos at 2× from Figma (select the layer → Export 2x), or ask Nico for the original stock files (the filenames suggest Envato-style stock, dated 2026) → **Q4**. Photos should ship as AVIF/WebP ≤ 250KB each at 1×.

## 8. Content model (ACF field group "Front page", location = front page)
| Field | Type | Default (verbatim from Figma) |
|---|---|---|
| `hero_eyebrow` | text | ALPHA SOLAR TECHNOLOGIES |
| `hero_heading` | text (allows `<em>`) | Built to be `<em>`seen`</em>`, designed to `<em>`perform`</em>`. |
| `hero_body` | textarea (new lines → `<br>`) | (as §4.2) |
| `hero_bg_image`, `hero_fg_image`, `hero_band_image` | image (ID) | exports |
| `announce_heading` | text | Our `<em>`refreshed identity`</em>` and new website is coming: `<em>`1 February 2027`</em>`. |
| `announce_button` | link (title, url, target) | ENQUIRE ABOUT INVESTMENT → #contact |
| `business_heading` | text | Technology with global potential. `<em>`A business model built around its use.`</em>` |
| `business_body` | textarea (paragraphs → `wpautop`) | (as §4.5) |
| `business_image` | image | export |
| `licensing_heading` | text | A licensing model with `<em>`revenue tied to every print`</em>` |
| `licensing_slides` | gallery/repeater (Pro) or `licensing_slide_1..3` (free) | slide 1 export |
| `licensing_body` | textarea | (as §4.6) |
| `licensing_button` | link | REQUEST INVESTMENT INFORMATION → #contact |
| `contact_heading` | text | `<em>`Discuss investment`</em>` with AST |
| `contact_intro` | textarea | Contact our team to discuss investment in ArchiSolar’s development and commercialisation. |
| `contact_form_shortcode` | text | `[contact-form-7 id="…" title="Investment enquiry"]` |
| `contact_privacy_label` | text | Privacy policy (URL from `get_privacy_policy_url()`) |
| `footer_text` | text | © Alpha Solar Technologies Limited |

Every template part escapes output (`esc_html`, `esc_url`, `esc_attr`, `wp_kses` for headings) and renders sensible defaults when a field is empty.

## 9. Contact form — Contact Form 7
### 9.1 Form definition (CF7 → Contact → "Investment enquiry")
```
<div class="ast-form__row ast-form__row--2">
  <p class="ast-field"><label for="ast-name" class="screen-reader-text">Full name (required)</label>
  [text* full-name id:ast-name autocomplete:name placeholder "Full name"]</p>
  <p class="ast-field"><label for="ast-email" class="screen-reader-text">Email address (required)</label>
  [email* email-address id:ast-email autocomplete:email placeholder "Email address"]</p>
</div>
<p class="ast-field"><label for="ast-company" class="screen-reader-text">Company or organisation (optional)</label>
[text company id:ast-company autocomplete:organization placeholder "Company or organisation (optional)"]</p>
<p class="ast-field ast-field--textarea"><label for="ast-enquiry" class="screen-reader-text">Your enquiry (required)</label>
[textarea* enquiry id:ast-enquiry x6 maxlength:2000 placeholder "Your enquiry"]</p>
<div class="ast-form__actions">
  <span class="ast-submit">[submit class:btn class:btn--primary "Enquire about investment"]</span>
  <a class="btn btn--outline" href="[privacy_url]">Privacy policy</a>
</div>
```
| Field | CF7 name | Type | Required | Placeholder (verbatim) | Validation |
|---|---|---|---|---|---|
| Full name | `full-name` | text* | yes [inferred: only Company says "(optional)"] | Full name | non-empty, max 100 (`maxlength:100`) |
| Email | `email-address` | email* | yes | Email address | CF7 email format |
| Company | `company` | text | no | Company or organisation (optional) | max 150 |
| Enquiry | `enquiry` | textarea* | yes | Your enquiry | non-empty, maxlength 2000 [PROPOSED] |
| Submit | — | submit | — | **Enquire about investment** (CSS uppercases it to match "ENQUIRE ABOUT INVESTMENT") | — |

- The Privacy policy link isn't a CF7 tag. Output it from the theme next to the submit (put the shortcode `[privacy_url]` through a small `wpcf7_form_elements` filter, or place the actions row in the template after the shortcode and position it with CSS). **Recommended:** the theme registers the `wpcf7_form_elements` filter in `inc/cf7.php` that replaces `[privacy_url]` with `esc_url( get_privacy_policy_url() )`.
- **Consent / acceptance checkbox:** not in the design. If AST needs it for privacy compliance, add `[acceptance privacy-consent]` → **Q5**.
- **Spam:** enable CF7's Cloudflare Turnstile or reCAPTCHA v3 integration, or at minimum a honeypot add-on → **Q5**.
- **Mail (tab 2):** To = **Q5** (recipient unknown); From `wordpress@<domain>`; Reply-To `[email-address]`; Subject `Investment enquiry — [full-name]`. Recommend an SMTP plugin (e.g. WP Mail SMTP) for deliverability. Optionally store submissions with Flamingo.
- Messages (tab 4), [PROPOSED] copy: success "Thank you. Your enquiry has been sent and our team will be in touch." Validation summary "Please check the highlighted fields and try again." Required field "Please fill in this field." Invalid email "Please enter a valid email address." Send failure "Sorry, your enquiry could not be sent. Please try again or email us directly." → Nico to confirm (Q5).

### 9.2 Embedding
- `section-contact.php` outputs `do_shortcode( get_field('contact_form_shortcode') )`. If it's empty, it outputs nothing (plus an admin-only notice for logged-in editors).
- Turn off CF7's stylesheet with `add_filter('wpcf7_load_css', '__return_false')`. Keep CF7 JS for AJAX submit, and load CF7 JS/CSS **only on the front page** (`wpcf7_load_js` filter conditional, or dequeue elsewhere).
- Turn off CF7's auto `<p>`/`<br>` (`define('WPCF7_AUTOP', false)` in `functions.php`) so the markup above is exact.

### 9.3 Styling CF7 to match the design (scoped under `.ast-contact .wpcf7`)
| Part | Spec |
|---|---|
| Layout | `.ast-form__row--2`: grid 2 columns 679px / 676px (use `1fr 1fr` with 19px column gap). Row gap 21px. Max width 1374, centred. `.wpcf7-form-control-wrap` is `display:block`. |
| Inputs (`input[type=text|email]`) | height **69px**, width 100%, bg `#FFF`, border 0, radius 5px, padding `0 31px`, Montserrat 400 18px, colour `#1E1E1E` [PROPOSED typed-text colour], `appearance:none` |
| Textarea | height **322px**, padding `31px 31px`, `resize: vertical`, min-height 200px |
| Placeholder | `#757575` (AA, 4.61:1; Figma `#828282` is 3.84:1), opacity 1 |
| Focus [PROPOSED] | `outline: 3px solid #FAC84F; outline-offset: 2px` (12.67:1 against `#0A0A0A`), no glow |
| Error, field (`.wpcf7-not-valid`, `aria-invalid="true"`) [PROPOSED] | `box-shadow: inset 0 0 0 2px #B3261E` (6.54:1 on white) **plus** the error text below (the error isn't shown by colour alone) |
| Error tip (`.wpcf7-not-valid-tip`) | Montserrat 400 14px / 150%, `#FF8A80` on `#0A0A0A` (8.67:1), margin-top 8px, preceded by a "!" icon (aria-hidden) |
| Response output (`.wpcf7-response-output`) | margin 32px auto 0, max-width 1374, padding 16px 24px, radius 5px, Montserrat 400 16px/150%, border 2px. States from the form class: `.invalid`/`.unaccepted`/`.failed`/`.aborted` → border and text `#FF8A80`; `.sent` → border and text `#7EE2A8`; `.spam` → `#FAC84F`. Hidden while empty. |
| Submit (`input.wpcf7-submit`) | The primary pill (360×66, `#FAC84F`, radius 9999, 600 16px uppercase, text left padding 30px, right padding 66px for the icon). `<input>` can't hold an icon, so the wrapper `.ast-submit` (position:relative, inline-block) draws the 36px black circle with the white ↗ via `::after` (SVG data URI) at right 15px, centred vertically, `pointer-events:none`. |
| Submitting (`.wpcf7-form.submitting`) | Submit `opacity:.6; cursor:progress`, `aria-busy="true"` set by our JS on the form |
| Spinner (`.wpcf7-spinner`) | Restyle as a 20px ring (2px `#FAC84F`, transparent top), placed right of the actions row (margin-left 16px), with `animation: spin .8s linear infinite`. Under reduced motion: no rotation; show a static ring plus the visually hidden text "Sending…". Hidden unless `.submitting`. |
| Privacy button | `.btn--outline`, 239×66, sitting 35px to the right of the submit; the pair centred (§4.7) |

### 9.4 Accessibility of errors
- Every control has a real `<label>` (visually hidden, because the design shows placeholders only; Q5 asks about visible labels). Placeholders are not the accessible name.
- CF7 5.4+ already sets `aria-required`, `aria-invalid` and links `.wpcf7-not-valid-tip` with `aria-describedby`, and it fills `.screen-reader-response` (`role="status"`/`aria-live="polite"`) with an error summary. **Keep `.screen-reader-response` (visually hidden, not `display:none`).** Hide the duplicate visual summary only if it's redundant (we keep it visible, per the table above).
- On invalid submit, move focus to the first invalid field (small listener on `wpcf7invalid`). On `wpcf7mailsent`, move focus to the response output (`tabindex="-1"`).
- Errors are never colour-only (text tip + 2px ring). Tip text meets 4.5:1 and the ring meets 3:1.
- The form works without JS: CF7 falls back to a normal POST and reload.

## 10. Responsive behaviour
**There are no designs below 1920. Everything here is [PROPOSED] → Q11: please add 1440 and 390 frames.**
| Breakpoint | Behaviour |
|---|---|
| ≥1920 | Exact Figma layout. Above 1920, centre content in the 1564 container; backgrounds stretch full width (photos `cover`, anchored right). |
| 1440 (pixel-check target) | Container padding 64px → content 1312. Positions scale from the container left. Type: H1 `clamp(48px, 3.75vw, 72px)` = 54px; H2 `clamp(28px, 2.0833vw, 40px)` = 30px; body 18px fixed; buttons fixed size. Hero text max-width 670 → 560px. Business image max 771 → keeps ratio 1.447 inside cols 7–12. Carousel 1374 → 100% of the inner container, ratio 3.154. Arrows sit 24px outside the slide; if there's no room (< 1280), they move inside the slide edges (16px inset). |
| 1024 | Hero text column 60%. Business section keeps 2 columns (text 5 / image 7). Form keeps 2 columns. |
| 768 | Business: text, then image full-width (gap 40px). Form row 1 to 1 column. Carousel arrows inside the image. Buttons row wraps, centred, gap 16px. |
| 390 (mobile check target) | Padding-inline 20px. Header: logo 120px wide (ratio kept), CTA 40px high, padding 12px 20px. Hero: min-height 100svh, eyebrow 14px (ALL CAPS label exception), H1 40px/95%, body 16px/150%, bg photo `object-position: 70% center` so the building stays visible, with a left-to-right scrim (see §12). Image band full-bleed 20px inset, ratio 16:9 crop. Sections padding-block 80px. H2 28px/120%. Buttons full width (max 360), height 56px. Fields: inputs 56px, textarea 200px. Footer 16px text, padding 32px 20px. Decorative lines (Vectors 1/2, Lines 1–3) hidden < 768. Hex textures scaled to 60%. |

## 11. Interactions and motion
**Scroll reveal (subtle):**
- Effect: `opacity 0 → 1` and `translateY(20px) → 0`, **500ms**, `cubic-bezier(0.22, 1, 0.36, 1)` (ease-out). Grouped items stagger **80ms** (`--d: calc(var(--i) * 80ms)`).
- Trigger: once, using an IntersectionObserver with `threshold: 0.15` and `rootMargin: 0px 0px -10% 0px`. Unobserve after it reveals. No libraries; about 20 lines of JS in `main.js`.
- **No-JS safe:** the hidden start state applies only when JS has run: `<html class="no-js">`, an inline head script swaps it to `js`, and the CSS is `.js [data-reveal]:not(.is-revealed){opacity:0;transform:translateY(20px)}`. If the observer is unsupported, reveal everything at once.
- **Reduced motion:** `@media (prefers-reduced-motion: reduce)` gives no transform, no transition and full opacity. The JS also skips setting up the observer. Smooth scroll is off as well.
- Above-the-fold hero content animates on load (not on scroll) with the same fade-up, so the LCP element (H1) is never at opacity 0 for long. **Exception:** the H1 itself is **not** animated, to protect LCP.

| Section | Elements with `data-reveal` (stagger index) |
|---|---|
| Header | none |
| Hero | eyebrow (0), body (1), scroll button (2). The H1 is static. |
| Image band | the image (single) |
| Announcement | H2 (0), button (1) |
| Business model | H2 (0), body (1), image (2) |
| Licensing | H2 (0), carousel (1), body (2), button (3) |
| Contact | H2 (0), intro (1), form rows (2, 3, 4), actions (5) |
| Footer | none |

**Other interactions**
- Anchor links (`#announcement`, `#contact`) use `scroll-behavior: smooth` (off under reduced motion) and `scroll-margin-top: 24px`.
- Button hover/focus/active as in §5. Transitions 150ms.
- Carousel as in §4.6. The image band and images have no hover effects.
- No parallax, no autoplay, no cursor effects.

## 12. Accessibility (WCAG 2.2 AA)
**Measured contrast (WCAG ratio, against the actual rendered Figma colours):**
| Pair | Ratio | Needs | Result → Fix |
|---|---|---|---|
| Hero H1 white on photo (lightest spot behind the text, `#A9A391`) | 2.52 | 3.0 (large) | ❌ → add a scrim |
| Hero H1 yellow `#FAC84F` on photo (lightest) | 1.61 | 3.0 | ❌ → scrim |
| Hero body white 18px on photo (lightest) | 2.52 (darkest area 7.69) | 4.5 | ❌ → scrim |
| Eyebrow `#D3B69C` 16px on photo | 1.31–1.82 | 4.5 | ❌ → scrim (4.68 with a 55% scrim) |
| **Fix [PROPOSED]:** left-side scrim on the hero photo, `linear-gradient(90deg, rgba(0,0,0,.55) 0%, rgba(0,0,0,.55) 40%, rgba(0,0,0,0) 65%)`, under the text and above the photo/glow | white 8.99, yellow 5.75, sand 4.68 | | ✅ (slightly darkens the left of the hero; Nico to approve, Q12) |
| Contact Us black on `#FAC84F` | 13.44 | 4.5 | ✅ |
| White / yellow on `#0A0A0A` | 19.80 / 12.67 | 3.0 (40px) | ✅ |
| Business H2 `#1E1E1E` on white | 16.53 | 3.0 | ✅ |
| Business H2 highlight `#D8A01A` on white | 2.32 | 3.0 | ❌ → `#8A6500` (5.28) **or** keep the gold and treat it as decorative (it isn't; it's text) → Q12 |
| Business body `#1E1E1E` on white | 16.53 | 4.5 | ✅ |
| Licensing H2 `#1E1E1E` on `#FAC84F` | 10.67 | 3.0 | ✅ |
| Licensing H2 highlight **white on `#FAC84F`** | 1.56 | 3.0 | ❌ → highlight in `#1E1E1E` with a white underline, or use `#000` text on a `#1E1E1E` chip with white text → **Q12** (default: render the highlight `#FFFFFF` text on a `#1E1E1E` inline highlight, padding 0 .15em) |
| Licensing body `#1E1E1E` on yellow | 10.67 | 4.5 | ✅ |
| Dark button label white on `#1E1E1E` | 16.67 | 4.5 | ✅ |
| Button labels black on `#FAC84F` | 13.44 | 4.5 | ✅ |
| Privacy label white on `#0A0A0A`, border `#FAC84F` | 19.80 / 12.67 | 4.5 / 3.0 | ✅ |
| Placeholder `#828282` on white | 3.84 | 4.5 | ❌ → `#757575` (4.61) |
| White fields vs `#0A0A0A` (non-text) | 19.80 | 3.0 | ✅ |
| Scroll chevron `#FAC84F` on `#1E1E1E`; carousel chevron white on `#1E1E1E` | 10.67 / 16.67 | 3.0 | ✅ |
| Arrow circles `#1E1E1E` on yellow (non-text) | 10.67 | 3.0 | ✅ |
| Footer white on black | 21.0 | 4.5 | ✅ |

**Other requirements**
- Landmarks: `<header>`, `<main>`, `<footer>`, and each section is `<section aria-labelledby>`. Exactly one `<h1>` (the hero); the other section titles are `<h2>`. Add a skip link "Skip to content".
- Decorative images/textures/lines: CSS backgrounds or `alt=""` + `aria-hidden`. Content images (band, business, slides): ACF alt text required. If the alt is missing, fall back to `""` and show a warning in admin.
- Logo link has an accessible name. Icon-only buttons have `aria-label`s. Icons inside labelled buttons are `aria-hidden`.
- Focus visible on every interactive element (§5), focus not obscured (2.4.11), target size ≥ 24×24 (all are ≥ 47px).
- The page works at 200% zoom and with 320px reflow (1.4.10). No horizontal scroll.
- `lang="en-AU"` [PROPOSED; copy uses AU/UK spelling] → confirm in Q8.

## 13. Performance and SEO
- Targets (mobile, Lighthouse): Performance ≥ 90, Accessibility 100, Best Practices ≥ 95, SEO 100. LCP < 2.5s, CLS < 0.05, INP < 200ms.
- LCP = the hero H1/photo. `fetchpriority="high"` and no lazy-loading on the hero bg and foreground; `loading="lazy" decoding="async"` on everything below the fold. Width/height set on every image.
- Images as AVIF/WebP with `srcset`. Transparent PNG cut-out → WebP alpha. Hex/texture PNGs → WebP, ≤ 80KB each.
- One CSS file (≤ 40KB min), one deferred JS file (≤ 8KB). No jQuery dependency from the theme (CF7 doesn't need it from v5.4+).
- Remove emoji scripts, oEmbed discovery and global block-library CSS on the front page (the page doesn't use blocks).
- SEO: `title-tag` support. Meta description and OG/Twitter tags from an SEO plugin (Yoast/RankMath, **Q13**), or a minimal theme fallback. `Organization` JSON-LD (name "Alpha Solar Technologies Limited"; URL/logo once known). Semantic headings as above. Favicon/site icon from Customizer (asset needed → Q4).

## 14. Acceptance criteria and QA checklist
**Theme**
- [ ] Theme activates with no PHP notices (WP_DEBUG on). Passes `php -l` on all files. No page builder installed or required.
- [ ] `front-page.php` renders sections only through `template-parts/section-*.php`, in the §4 order.
- [ ] All copy/images in §8 are editable in wp-admin, and the defaults match the Figma copy verbatim (including the curly apostrophe in "ArchiSolar’s").
- [ ] Header CTA comes from `wp_nav_menu('primary')`. Images come from the Media Library with `srcset`.
- [ ] CF7 form exists with the §9.1 fields. The shortcode is set via ACF. CF7 default CSS is off and the theme styles match §9.3.

**Pixel-fidelity check**
- [ ] **1920 × 4960:** take a full-page screenshot (Chrome, DPR 1, fonts loaded, reveal disabled via `?noreveal` or reduced motion) and overlay `reference-full-page-1920x4960.png` at 50% (e.g. PixelPerfect extension, or a `pixelmatch` script). Tolerances: element positions ±2px, sizes ±2px, section band boundaries ±2px, type metrics match (family/weight/size/LH/LS exact). Allowed deviations only: (a) the contrast fixes in §12, (b) the centred contact button pair (§4.7), (c) the balanced heading breaks (§6.5), (d) photo compression.
- [ ] **1440:** compare against a 1440 Figma frame if Nico adds one (Q11). Otherwise check against the §10 rules: container 1312, H1 54px, H2 30px, no overlaps or clipping, ratios preserved (band 3.584, business 1.447, slide 3.154).
- [ ] **390 mobile:** compare against a mobile frame if added (Q11). Otherwise check the §10 rules: no horizontal scroll, tap targets ≥ 44px, form single column, buttons full width.
- [ ] Also spot-check 1024 and 768.

**Function and a11y**
- [ ] All anchors scroll to the right sections. The carousel works by mouse, keyboard and touch; arrows hide with one slide.
- [ ] Form: required errors per field, invalid email error, success message, spinner during sending, focus moves to the first error or the success message. Mail arrives at the configured recipient. Works with JS off.
- [ ] axe DevTools: 0 serious/critical issues. Keyboard-only run-through of the whole page. VoiceOver (macOS/iOS) reads the form errors.
- [ ] Reduced motion: nothing moves. JS disabled: all content visible.
- [ ] Lighthouse targets in §13 on mobile and desktop.
- [ ] Cross-browser: latest Chrome, Safari (macOS + iOS), Firefox, Edge. `mask-image` uses the `-webkit-` prefix too.

**Process**
- [ ] Work on a new branch (e.g. `feat/alpha-solar-theme`). **Do not commit or push without the owner's direct go-ahead.** Report the build back to Desi/Nico with screenshots of the overlay diffs.

## 15. Open questions
1. **Carousel:** the arrows imply more slides, but only one image exists. How many slides, which images, and should it loop?
2. **ACF Pro licence:** is one available (Repeater/Gallery for slides)? If not, it's 3 fixed slide fields.
3. **Header CTA font:** "Contact Us" uses Suisse Int'l Book (commercial, only used here). Is there a licence/webfont, or switch to Montserrat 500?
4. **Assets:** can we get the original stock photos (for 2× retina), a favicon/site icon and an SVG source logo, if different from the export?
5. **Form:** who receives enquiries (To address)? Is a privacy consent checkbox needed? Which spam protection? Confirm the response message copy. Visible labels, or placeholders only (we add visually hidden labels either way)? Any CRM or mailing-list integration?
6. **Button targets:** should "REQUEST INVESTMENT INFORMATION" scroll to the form, or link to a document/download?
7. **Button heights:** the dark button is 73px and the primary is 66px. Intentional, or should they match?
8. **Footer and locale:** add the year or ABN to the © line? Confirm `en-AU`.
9. **Header:** should it be sticky on scroll? (Not shown.)
10. **Rule of 4:** keep the raw Figma spacing (e.g. 179, 19, 21, 35, 31) for fidelity, or normalise to multiples of 4?
11. **Missing frames:** please add 1440 and 390 (mobile) frames. §10 is a proposal until then.
12. **Contrast fixes:** approve the hero scrim, the darker gold `#8A6500`, and the treatment for the white highlight on yellow (§12).
13. **SEO plugin:** Yoast, RankMath or none (theme fallback)? Page title and meta description copy?
14. **Hosting/domain:** where is it deployed (local/WP Engine), and which PHP/WP versions are targeted (assume PHP 8.1+, WP 6.6+)?
15. **"Privacy policy" page:** does the content exist, or should a placeholder page be created?
