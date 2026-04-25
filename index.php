<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Nangka — Marikina City</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ============================================================
   BARANGAY NANGKA — LANDING PAGE
   Theme: Refined civic pride — deep navy + warm gold + clean white
   ============================================================ */

:root {
  --navy: #0f2340;
  --navy-mid: #1a3a5c;
  --navy-light: #264d7a;
  --gold: #c9922a;
  --gold-light: #f0b840;
  --gold-pale: #fdf3e0;
  --white: #ffffff;
  --off-white: #f8f6f2;
  --gray-100: #f0ece6;
  --gray-300: #c8c0b4;
  --gray-500: #8a8078;
  --gray-700: #3d3530;
  --text: #1a1410;
  --radius: 12px;
  --radius-lg: 20px;
  --shadow: 0 4px 24px rgba(15,35,64,0.10);
  --shadow-lg: 0 12px 48px rgba(15,35,64,0.18);
  --transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
html { scroll-behavior: smooth; font-size: 16px; }

body {
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  background: var(--white);
  overflow-x: hidden;
}

/* ============================================================
   NAVBAR
   ============================================================ */
.navbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 1000;
  padding: 0 5%;
  height: 76px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: var(--transition);
  background: linear-gradient(to bottom, rgba(8,16,32,0.88) 0%, rgba(8,16,32,0.0) 100%);
}

.navbar.scrolled {
  background: rgba(8,16,32,0.97);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  box-shadow: 0 2px 24px rgba(0,0,0,0.35);
  height: 64px;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.nav-logo {
  width: 46px; height: 46px;
  background: transparent;
  border-radius: 0;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: none;
}

.nav-logo img {
  width: 46px; height: 46px;
  object-fit: contain;
  filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));
}

.nav-logo i {
  font-size: 28px;
  color: var(--gold-light);
  filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));
}

.nav-title {
  display: flex;
  flex-direction: column;
}

.nav-title .main {
  font-family: 'Playfair Display', serif;
  font-size: 16px;
  font-weight: 700;
  color: var(--white);
  line-height: 1.1;
}

.nav-title .sub {
  font-size: 10px;
  color: rgba(255,255,255,0.55);
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 32px;
  list-style: none;
}

.nav-links a {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 500;
  letter-spacing: 0.3px;
  transition: var(--transition);
  position: relative;
}

.nav-links a::after {
  content: '';
  position: absolute;
  bottom: -4px; left: 0; right: 0;
  height: 2px;
  background: var(--gold-light);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.3s ease;
}

.nav-links a:hover { color: var(--white); }
.nav-links a:hover::after { transform: scaleX(1); }

.btn-login-nav {
  background: var(--gold);
  color: var(--navy) !important;
  padding: 9px 22px;
  border-radius: 8px;
  font-weight: 700 !important;
  font-size: 13px !important;
  box-shadow: 0 4px 12px rgba(201,146,42,0.35);
  transition: var(--transition) !important;
}

.btn-login-nav:hover {
  background: var(--gold-light) !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(201,146,42,0.45) !important;
}

.btn-login-nav::after { display: none !important; }

.nav-hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  cursor: pointer;
  background: none;
  border: none;
  padding: 4px;
}

.nav-hamburger span {
  display: block;
  width: 24px; height: 2px;
  background: white;
  border-radius: 2px;
  transition: var(--transition);
}

/* ============================================================
   HERO CAROUSEL
   ============================================================ */
.hero {
  position: relative;
  width: 100vw;
  height: 100vh;
  min-height: 620px;
  overflow: hidden;
  margin: 0;
  padding: 0;
  left: 0;
  top: 0;
}

.carousel-track {
  display: flex;
  width: 100%;
  height: 100%;
  transition: transform 0.9s cubic-bezier(0.77,0,0.175,1);
  will-change: transform;
}

.carousel-slide {
  min-width: 100%;
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
}

/* Fallback if image fails to load */
.carousel-slide { background-color: #0f2340; }

.slide-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(15,35,64,0.3) 0%,
    rgba(15,35,64,0.15) 40%,
    rgba(15,35,64,0.7) 100%
  );
}

.slide-pattern {
  position: absolute;
  inset: 0;
  opacity: 0.04;
  background-image: repeating-linear-gradient(
    45deg,
    #fff 0, #fff 1px,
    transparent 0, transparent 50%
  );
  background-size: 20px 20px;
}

/* Slide decorative icons */
.slide-deco {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 220px;
  color: rgba(255,255,255,0.04);
  pointer-events: none;
}

.hero-content {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  padding: 0 8% 80px;
  z-index: 10;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(201,146,42,0.25);
  border: 1px solid rgba(201,146,42,0.5);
  color: var(--gold-light);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  padding: 6px 14px;
  border-radius: 20px;
  margin-bottom: 20px;
  animation: fadeUp 1s ease 0.2s both;
}

.hero-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(38px, 6vw, 76px);
  font-weight: 900;
  color: var(--white);
  line-height: 1.05;
  margin-bottom: 20px;
  animation: fadeUp 1s ease 0.4s both;
}

.hero-title span {
  color: var(--gold-light);
  display: block;
}

.hero-subtitle {
  font-size: clamp(14px, 1.5vw, 17px);
  color: rgba(255,255,255,0.75);
  max-width: 540px;
  line-height: 1.7;
  margin-bottom: 36px;
  animation: fadeUp 1s ease 0.6s both;
}

.hero-actions {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
  animation: fadeUp 1s ease 0.8s both;
}

.btn-hero-primary {
  background: var(--gold);
  color: var(--navy);
  padding: 14px 30px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 6px 24px rgba(201,146,42,0.45);
  transition: var(--transition);
}

.btn-hero-primary:hover {
  background: var(--gold-light);
  transform: translateY(-2px);
  box-shadow: 0 10px 32px rgba(201,146,42,0.55);
}

.btn-hero-outline {
  background: transparent;
  color: var(--white);
  padding: 14px 30px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 14px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: 1.5px solid rgba(255,255,255,0.5);
  transition: var(--transition);
}

.btn-hero-outline:hover {
  background: rgba(255,255,255,0.12);
  border-color: white;
}

/* Carousel controls */
.carousel-nav {
  position: absolute;
  bottom: 32px;
  right: 8%;
  display: flex;
  gap: 8px;
  z-index: 20;
}

.carousel-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: rgba(255,255,255,0.35);
  cursor: pointer;
  transition: var(--transition);
  border: none;
}

.carousel-dot.active {
  background: var(--gold-light);
  width: 28px;
  border-radius: 4px;
}

.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 20;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.2);
  color: white;
  width: 48px; height: 48px;
  border-radius: 50%;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  transition: var(--transition);
  backdrop-filter: blur(8px);
}

.carousel-arrow:hover { background: rgba(255,255,255,0.25); }
.carousel-prev { left: 3%; }
.carousel-next { right: 3%; }

/* Scroll indicator */
.scroll-hint {
  position: absolute;
  bottom: 36px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: rgba(255,255,255,0.5);
  font-size: 10px;
  letter-spacing: 2px;
  text-transform: uppercase;
  z-index: 20;
  animation: bounce 2s ease infinite;
}

.scroll-hint i { font-size: 14px; }

/* ============================================================
   SECTION COMMON
   ============================================================ */
section { padding: 96px 8%; }

.section-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--gold);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 14px;
}

.section-eyebrow::before {
  content: '';
  width: 28px; height: 2px;
  background: var(--gold);
  border-radius: 2px;
}

.section-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(28px, 3.5vw, 44px);
  font-weight: 700;
  line-height: 1.15;
  color: var(--navy);
  margin-bottom: 16px;
}

.section-title.light { color: var(--white); }

.section-lead {
  font-size: 16px;
  color: var(--gray-500);
  line-height: 1.8;
  max-width: 600px;
}

.section-lead.light { color: rgba(255,255,255,0.65); }

/* Reveal animation */
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}
.reveal.visible {
  opacity: 1;
  transform: translateY(0);
}

/* ============================================================
   ABOUT SECTION
   ============================================================ */
.about {
  background: var(--white);
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
  padding: 96px 8%;
}

.about-visual {
  position: relative;
}

.about-img-frame {
  width: 100%;
  aspect-ratio: 4/3;
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
  border-radius: var(--radius-lg);
  overflow: hidden;
  position: relative;
  box-shadow: var(--shadow-lg);
}

.about-img-frame i {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 100px;
  color: rgba(255,255,255,0.08);
}

.about-img-frame::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 50%;
  background: linear-gradient(to top, rgba(15,35,64,0.6), transparent);
}

.about-badge {
  position: absolute;
  bottom: -20px; right: -20px;
  background: var(--gold);
  color: var(--navy);
  padding: 20px 24px;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  text-align: center;
  min-width: 130px;
}

.about-badge .num {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 900;
  line-height: 1;
}

.about-badge .lbl {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-top: 4px;
  opacity: 0.8;
}

.about-stat-row {
  display: flex;
  gap: 32px;
  margin-top: 36px;
  flex-wrap: wrap;
}

.about-stat {
  flex: 1;
  min-width: 120px;
  border-left: 3px solid var(--gold);
  padding-left: 16px;
}

.about-stat .val {
  font-family: 'Playfair Display', serif;
  font-size: 28px;
  font-weight: 700;
  color: var(--navy);
}

.about-stat .lbl {
  font-size: 12px;
  color: var(--gray-500);
  margin-top: 2px;
}

.about-text p {
  font-size: 15.5px;
  color: var(--gray-500);
  line-height: 1.85;
  margin-bottom: 16px;
}

.about-officials {
  display: flex;
  gap: 12px;
  margin-top: 24px;
  flex-wrap: wrap;
}

.official-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--off-white);
  border: 1px solid var(--gray-100);
  border-radius: 8px;
  padding: 8px 14px;
  font-size: 12.5px;
}

.official-chip .avatar {
  width: 28px; height: 28px;
  background: var(--navy);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: var(--gold);
  font-size: 11px;
  font-weight: 700;
  flex-shrink: 0;
}

.official-chip .name { font-weight: 600; color: var(--navy); }
.official-chip .role { font-size: 11px; color: var(--gray-500); }

/* ============================================================
   SERVICES
   ============================================================ */
.services {
  background: var(--navy);
  position: relative;
  overflow: hidden;
}

.services::before {
  content: '';
  position: absolute;
  top: -100px; right: -100px;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(201,146,42,0.12) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.services::after {
  content: '';
  position: absolute;
  bottom: -80px; left: -80px;
  width: 400px; height: 400px;
  background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-top: 48px;
}

.service-card {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: var(--radius);
  padding: 28px 24px;
  cursor: pointer;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  text-decoration: none;
}

.service-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(201,146,42,0.15), transparent);
  opacity: 0;
  transition: var(--transition);
}

.service-card:hover {
  background: rgba(255,255,255,0.09);
  border-color: rgba(201,146,42,0.4);
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.25);
}

.service-card:hover::before { opacity: 1; }

.service-icon {
  width: 52px; height: 52px;
  background: rgba(201,146,42,0.2);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
  color: var(--gold-light);
  margin-bottom: 18px;
  transition: var(--transition);
}

.service-card:hover .service-icon {
  background: var(--gold);
  color: var(--navy);
  transform: scale(1.08);
}

.service-card h3 {
  font-size: 15px;
  font-weight: 700;
  color: var(--white);
  margin-bottom: 8px;
}

.service-card p {
  font-size: 13px;
  color: rgba(255,255,255,0.5);
  line-height: 1.6;
}

.service-card .service-tag {
  display: inline-block;
  margin-top: 16px;
  font-size: 10.5px;
  font-weight: 700;
  color: var(--gold-light);
  text-transform: uppercase;
  letter-spacing: 1px;
  opacity: 0;
  transform: translateY(4px);
  transition: var(--transition);
}

.service-card:hover .service-tag {
  opacity: 1;
  transform: translateY(0);
}

/* ============================================================
   MAP SECTION
   ============================================================ */
.map-section {
  background: var(--off-white);
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 0;
  padding: 0;
  min-height: 520px;
}

.map-info {
  padding: 64px 8%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: var(--white);
}

.map-info .section-lead {
  margin-top: 16px;
  margin-bottom: 28px;
}

.address-block {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
}

.address-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  font-size: 14px;
  color: var(--gray-700);
}

.address-item i {
  width: 36px; height: 36px;
  background: var(--gold-pale);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: var(--gold);
  font-size: 14px;
  flex-shrink: 0;
  margin-top: 2px;
}

.address-item strong { display: block; color: var(--navy); font-weight: 600; }

.btn-directions {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--navy);
  color: var(--white);
  padding: 12px 24px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 13.5px;
  text-decoration: none;
  width: fit-content;
  transition: var(--transition);
}

.btn-directions:hover {
  background: var(--navy-mid);
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(15,35,64,0.25);
}

.map-embed {
  position: relative;
  overflow: hidden;
}

.map-embed iframe {
  width: 100%;
  height: 100%;
  min-height: 520px;
  border: none;
  display: block;
  filter: saturate(0.9);
}

/* ============================================================
   HOTLINES
   ============================================================ */
.hotlines {
  background: var(--white);
}

.hotlines-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
  margin-top: 48px;
}

.hotline-group {
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--gray-100);
  box-shadow: var(--shadow);
}

.hotline-group-header {
  padding: 18px 22px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 700;
  font-size: 14px;
}

.hotline-group-header i {
  width: 36px; height: 36px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.hotline-group.emergency .hotline-group-header { background: #dc3545; color: white; }
.hotline-group.emergency .hotline-group-header i { background: rgba(255,255,255,0.2); color: white; }
.hotline-group.legal .hotline-group-header { background: var(--navy); color: white; }
.hotline-group.legal .hotline-group-header i { background: rgba(255,255,255,0.15); color: var(--gold-light); }
.hotline-group.health .hotline-group-header { background: #28a745; color: white; }
.hotline-group.health .hotline-group-header i { background: rgba(255,255,255,0.2); color: white; }

.hotline-list { padding: 8px 0; }

.hotline-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 13px 22px;
  border-bottom: 1px solid var(--gray-100);
  transition: var(--transition);
}

.hotline-item:last-child { border-bottom: none; }
.hotline-item:hover { background: var(--off-white); }

.hotline-name { font-size: 13.5px; font-weight: 600; color: var(--navy); }
.hotline-sub { font-size: 11.5px; color: var(--gray-500); margin-top: 1px; }

.hotline-number {
  font-size: 14px;
  font-weight: 700;
  color: var(--navy);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
}

.hotline-number:hover { color: var(--gold); }
.hotline-number i { font-size: 12px; color: var(--gold); }

/* ============================================================
   SOCIAL MEDIA
   ============================================================ */
.social-section {
  background: var(--off-white);
  text-align: center;
}

.social-section .section-title,
.social-section .section-eyebrow { margin: 0 auto; }

.social-section .section-eyebrow { display: flex; justify-content: center; }

.social-section .section-lead {
  margin: 16px auto 48px;
  text-align: center;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 48px;
}

.social-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 28px 32px;
  background: var(--white);
  border-radius: var(--radius);
  border: 1px solid var(--gray-100);
  text-decoration: none;
  width: 150px;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.social-card i {
  font-size: 28px;
  transition: var(--transition);
}

.social-card span {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--gray-700);
}

.social-card.fb { --sc: #1877f2; }
.social-card.yt { --sc: #ff0000; }
.social-card.tw { --sc: #1da1f2; }
.social-card.ig { --sc: #e1306c; }
.social-card.tg { --sc: #2ca5e0; }

.social-card i { color: var(--sc); }

.social-card:hover {
  background: var(--sc);
  border-color: var(--sc);
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.social-card:hover i,
.social-card:hover span { color: white; }

/* Social embed window */
.social-embed-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  max-width: 960px;
  margin: 0 auto;
}

.social-embed-box iframe {
  width: 100%;
  display: block;
}

@media (max-width: 768px) {
  .social-embed-row {
    grid-template-columns: 1fr;
  }
}

.social-embed-box {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--gray-100);
  box-shadow: var(--shadow);
}

.social-embed-header {
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid var(--gray-100);
  font-size: 13px;
  font-weight: 700;
  color: var(--navy);
}

.social-embed-header i { color: var(--sc, #1877f2); font-size: 16px; }

.social-embed-placeholder {
  height: 280px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: var(--off-white);
  color: var(--gray-300);
}

.social-embed-placeholder i { font-size: 40px; }
.social-embed-placeholder p { font-size: 12.5px; }

/* ============================================================
   CONTACT FORM
   ============================================================ */
.contact-section {
  background: var(--navy);
  position: relative;
  overflow: hidden;
}

.contact-section::before {
  content: '';
  position: absolute;
  top: -100px; right: -100px;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(201,146,42,0.1) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.contact-inner {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 80px;
  align-items: start;
  position: relative;
  z-index: 1;
}

.contact-info .section-lead {
  max-width: 400px;
  margin: 16px 0 32px;
}

.contact-detail-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.contact-detail {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.contact-detail-icon {
  width: 40px; height: 40px;
  background: rgba(201,146,42,0.2);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: var(--gold-light);
  font-size: 16px;
  flex-shrink: 0;
}

.contact-detail-text strong {
  display: block;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: rgba(255,255,255,0.45);
  margin-bottom: 3px;
}

.contact-detail-text span {
  font-size: 14.5px;
  color: rgba(255,255,255,0.85);
  font-weight: 500;
}

/* Form */
.contact-form-card {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: var(--radius-lg);
  padding: 40px;
  backdrop-filter: blur(10px);
}

.contact-form-card h3 {
  font-family: 'Playfair Display', serif;
  font-size: 22px;
  font-weight: 700;
  color: var(--white);
  margin-bottom: 28px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.contact-form-card h3 i { color: var(--gold-light); }

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 7px;
  margin-bottom: 16px;
}

.form-group.full { grid-column: 1 / -1; }

.form-group label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: rgba(255,255,255,0.55);
}

.form-group input,
.form-group select,
.form-group textarea {
  background: rgba(255,255,255,0.08);
  border: 1.5px solid rgba(255,255,255,0.15);
  border-radius: 10px;
  padding: 12px 16px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: var(--white);
  transition: var(--transition);
  width: 100%;
}

.form-group input::placeholder,
.form-group textarea::placeholder { color: rgba(255,255,255,0.3); }

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--gold-light);
  background: rgba(255,255,255,0.12);
  box-shadow: 0 0 0 3px rgba(201,146,42,0.15);
}

.form-group select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='rgba(255,255,255,0.4)' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
}

.form-group select option { background: var(--navy-mid); color: white; }

.form-group textarea { resize: vertical; min-height: 110px; }

.btn-submit {
  width: 100%;
  padding: 14px;
  background: var(--gold);
  color: var(--navy);
  border: none;
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: var(--transition);
  box-shadow: 0 6px 20px rgba(201,146,42,0.35);
  margin-top: 4px;
}

.btn-submit:hover {
  background: var(--gold-light);
  transform: translateY(-1px);
  box-shadow: 0 10px 30px rgba(201,146,42,0.45);
}

.form-success {
  display: none;
  text-align: center;
  padding: 24px;
  color: var(--white);
}

.form-success i { font-size: 40px; color: #5dd879; margin-bottom: 12px; }
.form-success h4 { font-size: 18px; margin-bottom: 8px; }
.form-success p { font-size: 14px; color: rgba(255,255,255,0.6); }

/* ============================================================
   FOOTER
   ============================================================ */
footer {
  background: #070f1d;
  color: rgba(255,255,255,0.55);
  padding: 48px 8% 28px;
}

.footer-top {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr;
  gap: 48px;
  margin-bottom: 40px;
  padding-bottom: 40px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.footer-brand .nav-title .main { font-size: 18px; }
.footer-brand .desc {
  margin-top: 14px;
  font-size: 13.5px;
  line-height: 1.8;
  color: rgba(255,255,255,0.45);
  max-width: 300px;
}

.footer-social-mini {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.footer-social-mini a {
  width: 34px; height: 34px;
  background: rgba(255,255,255,0.08);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,0.5);
  font-size: 13px;
  text-decoration: none;
  transition: var(--transition);
}

.footer-social-mini a:hover { background: var(--gold); color: var(--navy); }

.footer-col h4 {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,0.35);
  margin-bottom: 16px;
}

.footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.footer-col ul li a {
  font-size: 13.5px;
  color: rgba(255,255,255,0.5);
  text-decoration: none;
  transition: color 0.2s;
}
.footer-col ul li a:hover { color: var(--gold-light); }

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12.5px;
  color: rgba(255,255,255,0.3);
  flex-wrap: wrap;
  gap: 10px;
}

.footer-bottom a { color: var(--gold); text-decoration: none; }

/* ============================================================
   MOBILE NAV DRAWER
   ============================================================ */
.mobile-nav {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 2000;
  pointer-events: none;
}

.mobile-nav.open { pointer-events: all; }

.mobile-nav-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.6);
  opacity: 0;
  transition: opacity 0.35s;
}

.mobile-nav.open .mobile-nav-overlay { opacity: 1; }

.mobile-nav-drawer {
  position: absolute;
  right: 0; top: 0; bottom: 0;
  width: 280px;
  background: var(--navy);
  padding: 28px 24px;
  transform: translateX(100%);
  transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
  overflow-y: auto;
}

.mobile-nav.open .mobile-nav-drawer { transform: translateX(0); }

.mobile-nav-close {
  background: none;
  border: none;
  color: rgba(255,255,255,0.5);
  font-size: 20px;
  cursor: pointer;
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.mobile-nav-links {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.mobile-nav-links a {
  display: block;
  padding: 12px 16px;
  color: rgba(255,255,255,0.75);
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  border-radius: 8px;
  transition: var(--transition);
}

.mobile-nav-links a:hover { background: rgba(255,255,255,0.08); color: white; }

.mobile-nav-links .login-link {
  background: var(--gold);
  color: var(--navy) !important;
  font-weight: 700;
  margin-top: 12px;
  text-align: center;
}

/* ============================================================
   ANIMATIONS
   ============================================================ */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50% { transform: translateX(-50%) translateY(6px); }
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1024px) {
  .about { grid-template-columns: 1fr; gap: 48px; }
  .about-visual { max-width: 500px; }
  .contact-inner { grid-template-columns: 1fr; gap: 40px; }
  .map-section { grid-template-columns: 1fr; }
  .map-embed iframe { min-height: 350px; }
  .footer-top { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 768px) {
  .nav-links { display: none; }
  .nav-hamburger { display: flex; }
  .mobile-nav { display: block; }
  section { padding: 64px 5%; }
  .about { padding: 64px 5%; }
  .form-row { grid-template-columns: 1fr; }
  .footer-top { grid-template-columns: 1fr; gap: 32px; }
  .footer-bottom { flex-direction: column; text-align: center; }
  .contact-form-card { padding: 24px; }
  .hero-content { padding: 0 5% 70px; }
  .carousel-arrow { display: none; }
}
</style>
</head>

<body>

<!-- ============================================================
     NAVBAR
     ============================================================ -->
<nav class="navbar" id="navbar">
  <a href="#" class="nav-brand">
    <div class="nav-logo">
      <img src="assets/img/logo.png" alt="Barangay Nangka Logo">
    </div>
    <div class="nav-title">
      <span class="main">Barangay Nangka</span>
      <span class="sub">Marikina City</span>
    </div>
  </a>

  <ul class="nav-links">
    <li><a href="#about">About</a></li>
    <li><a href="#services">Services</a></li>
    <li><a href="#location">Location</a></li>
    <li><a href="#hotlines">Hotlines</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="login.php" class="btn-login-nav"><i class="fas fa-right-to-bracket"></i>Login Here</a></li>
  </ul>

  <button class="nav-hamburger" id="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-overlay" id="navOverlay"></div>
  <div class="mobile-nav-drawer">
    <button class="mobile-nav-close" id="navClose"><i class="fas fa-times"></i> Close</button>
    <ul class="mobile-nav-links">
      <li><a href="#about" onclick="closeMobileNav()">About</a></li>
      <li><a href="#services" onclick="closeMobileNav()">Services</a></li>
      <li><a href="#location" onclick="closeMobileNav()">Location</a></li>
      <li><a href="#hotlines" onclick="closeMobileNav()">Hotlines</a></li>
      <li><a href="#contact" onclick="closeMobileNav()">Contact</a></li>
      <li><a href="login.php" class="login-link"><i class="fas fa-right-to-bracket"></i> Staff Login</a></li>
    </ul>
  </div>
</div>

<!-- ============================================================
     HERO CAROUSEL
     ============================================================ -->
<section class="hero" id="home">
  <div class="carousel-track" id="carouselTrack">

    <!-- Slide 1 -->
    <div class="carousel-slide" style="background-image: url('assets/img/nangka1.jpg'); background-color:#0f2340;">
      <div class="slide-overlay"></div>
    </div>

    <!-- Slide 2 -->
    <div class="carousel-slide" style="background-image: url('assets/img/nangka2.jpg'); background-color:#0f2340; background-position: center 30%;">
      <div class="slide-overlay"></div>
    </div>

    <!-- Slide 3 -->
    <div class="carousel-slide" style="background-image: url('assets/img/nangka3.jpg'); background-color:#0f2340; background-position: center 60%;">
      <div class="slide-overlay"></div>
    </div>

    <!-- Slide 4 -->
    <div class="carousel-slide" style="background-image: url('assets/img/nangka4.jpg'); background-color:#0f2340;">
      <div class="slide-overlay"></div>
    </div>
  </div>

  <!-- Hero Content (on top of carousel) -->
  <div class="hero-content">
    <div class="hero-eyebrow">
      <i class="fas fa-location-dot"></i>
      Marikina City, Metro Manila
    </div>
    <h1 class="hero-title">
      Barangay
      <span>Nangka</span>
    </h1>
    <p class="hero-subtitle">
      Serving our community with transparency, efficiency, and excellence.
      Your neighborhood, your home — digitally connected.
    </p>
    <div class="hero-actions">
      <a href="#services" class="btn-hero-primary">
        <i class="fas fa-file-certificate"></i>
        Request Documents
      </a>
      <a href="#about" class="btn-hero-outline">
        <i class="fas fa-info-circle"></i>
        About Our Barangay
      </a>
    </div>
  </div>

  <!-- Carousel Controls -->
  <button class="carousel-arrow carousel-prev" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
  <button class="carousel-arrow carousel-next" id="nextBtn"><i class="fas fa-chevron-right"></i></button>

  <div class="carousel-nav" id="carouselDots"></div>

  <div class="scroll-hint">
    <span>Scroll</span>
    <i class="fas fa-chevron-down"></i>
  </div>
</section>

<!-- ============================================================
     ABOUT SECTION
     ============================================================ -->
<section class="about" id="about">
  <div class="about-visual reveal">
    <div class="about-img-frame">
      <img src="assets/img/nangkaabout.jpg" style="width:100%;height:100%;object-fit:cover;">
      <i class="fas fa-city" style="font-size:100px;color:rgba(255,255,255,0.08);position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"></i>
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(15,35,64,0.5),transparent);"></div>
      <div style="position:absolute;bottom:24px;left:24px;z-index:2;">
        <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:white;">Barangay Nangka</div>
        <div style="font-size:12px;color:rgba(255,255,255,0.65);margin-top:2px;">Marikina City, Metro Manila</div>
      </div>
    </div>
    <div class="about-badge">
      <div class="num">1959</div>
      <div class="lbl">EST</div>
    </div>
  </div>

  <div class="about-text reveal">
    <div class="section-eyebrow">About Us</div>
    <h2 class="section-title">Serving the Community of Nangka</h2>

    <p>Barangay Nangka is one of the vibrant barangays of Marikina City, known for its close-knit community, active civic participation, and rich local culture. Located in the heart of Marikina — the Shoe Capital of the Philippines — Nangka is home to thousands of families and small businesses.</p>

    <p>Our barangay hall is committed to delivering fast, transparent, and accessible public services to all residents. Through this digital platform, we aim to bridge the gap between government and the community we serve.</p>

    <div class="about-stat-row">
      <div class="about-stat">
        <div class="val">Marikina</div>
        <div class="lbl">City</div>
      </div>
      <div class="about-stat">
        <div class="val">Metro Manila</div>
        <div class="lbl">Province / Region</div>
      </div>
      <div class="about-stat">
        <div class="val">1808</div>
        <div class="lbl">ZIP Code</div>
      </div>
    </div>

    <div class="about-officials">
      <div class="official-chip">
        <div class="avatar">PK</div>
        <div>
          <div class="name">Punong Barangay</div>
          <div class="role">Barangay Captain</div>
        </div>
      </div>
      <div class="official-chip">
        <div class="avatar">SK</div>
        <div>
          <div class="name">Barangay Secretary</div>
          <div class="role">Administration</div>
        </div>
      </div>
      <div class="official-chip">
        <div class="avatar">TR</div>
        <div>
          <div class="name">Barangay Treasurer</div>
          <div class="role">Finance</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     SERVICES
     ============================================================ -->
<section class="services" id="services">
  <div class="reveal">
    <div class="section-eyebrow" style="color:var(--gold-light);"><span style="background:var(--gold-light);"></span> Online Services</div>
    <h2 class="section-title light">Barangay Online Services</h2>
    <p class="section-lead light">Request official barangay documents quickly and conveniently. Visit the barangay hall or use our system for faster processing.</p>
  </div>

  <div class="services-grid">
    <a href="login.php" class="service-card reveal">
      <div class="service-icon"><i class="fas fa-file-shield"></i></div>
      <h3>Barangay Clearance</h3>
      <p>Required for employment, business permits, and other legal transactions. Processing time: 5–10 minutes.</p>
      <span class="service-tag"><i class="fas fa-arrow-right"></i> Request Now</span>
    </a>

    <a href="login.php" class="service-card reveal">
      <div class="service-icon"><i class="fas fa-store"></i></div>
      <h3>Business Permit Clearance</h3>
      <p>Obtain barangay clearance for new and renewing business permits and Mayor's business permits.</p>
      <span class="service-tag"><i class="fas fa-arrow-right"></i> Request Now</span>
    </a>

    <a href="login.php" class="service-card reveal">
      <div class="service-icon"><i class="fas fa-house-circle-check"></i></div>
      <h3>Certificate of Residency</h3>
      <p>Proof of residency for school enrollment, bank account opening, and government transactions.</p>
      <span class="service-tag"><i class="fas fa-arrow-right"></i> Request Now</span>
    </a>

    <a href="login.php" class="service-card reveal">
      <div class="service-icon"><i class="fas fa-hand-holding-heart"></i></div>
      <h3>Indigency Certificate</h3>
      <p>For residents requiring financial assistance, scholarships, and medical assistance programs.</p>
      <span class="service-tag"><i class="fas fa-arrow-right"></i> Request Now</span>
    </a>

    <a href="login.php" class="service-card reveal">
      <div class="service-icon"><i class="fas fa-user-check"></i></div>
      <h3>Good Moral Character</h3>
      <p>Certificate attesting to the good standing and moral character of a resident in the community.</p>
      <span class="service-tag"><i class="fas fa-arrow-right"></i> Request Now</span>
    </a>

    <a href="login.php" class="service-card reveal">
      <div class="service-icon"><i class="fas fa-triangle-exclamation"></i></div>
      <h3>File a Complaint</h3>
      <p>Report community concerns, neighbor disputes, and incidents to the barangay for proper resolution.</p>
      <span class="service-tag"><i class="fas fa-arrow-right"></i> File Now</span>
    </a>
  </div>

  <div style="text-align:center;margin-top:40px;" class="reveal">
    <a href="login.php" style="display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:var(--navy);padding:14px 32px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;box-shadow:0 6px 24px rgba(201,146,42,0.4);transition:var(--transition);"
       onmouseover="this.style.background='#f0b840'" onmouseout="this.style.background='#c9922a'">
      <i class="fas fa-right-to-bracket"></i>
      Login to Request Documents
    </a>
  </div>
</section>

<!-- ============================================================
     MAP SECTION
     ============================================================ -->
<section class="map-section" id="location" style="padding:0;">
  <div class="map-info reveal">
    <div class="section-eyebrow">Location</div>
    <h2 class="section-title">Find Us</h2>
    <p class="section-lead">Visit Barangay Nangka Hall for in-person services, document releases, and community concerns.</p>

    <div class="address-block">
      <div class="address-item">
        <i class="fas fa-location-dot"></i>
        <div>
          <strong>Address</strong>
          9 Old J.P. Rizal, Nangka, Marikina City, 1808 Metro Manila, Philippines
        </div>
      </div>
      <div class="address-item">
        <i class="fas fa-clock"></i>
        <div>
          <strong>Office Hours</strong>
          Monday – Friday, 8:00 AM – 5:00 PM
        </div>
      </div>
      <div class="address-item">
        <i class="fas fa-phone"></i>
        <div>
          <strong>Contact</strong>
          (02) 8XXX-XXXX · barangaynangka@marikina.gov.ph
        </div>
      </div>
    </div>

    <a href="https://maps.google.com/?q=Nangka+Barangay+Hall+Marikina+City" target="_blank" class="btn-directions">
      <i class="fas fa-diamond-turn-right"></i>
      Get Directions
    </a>
  </div>

  <div class="map-embed">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.0!2d121.10869!3d14.67336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b98d8d4e72bd%3A0xb9e392279cc8cef!2sNangka%20Barangay%20Hall!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      allowfullscreen>
    </iframe>
  </div>
</section>

<!-- ============================================================
     HOTLINES
     ============================================================ -->
<section class="hotlines" id="hotlines">
  <div class="reveal" style="text-align:center;margin-bottom:0;">
    <div class="section-eyebrow" style="justify-content:center;display:flex;">Emergency &amp; Legal Hotlines</div>
    <h2 class="section-title" style="text-align:center;">Important Hotlines</h2>
    <p class="section-lead" style="margin:14px auto 0;text-align:center;">Save these numbers. In case of emergencies, call immediately.</p>
  </div>

  <div class="hotlines-grid">
    <!-- Emergency -->
    <div class="hotline-group emergency reveal">
      <div class="hotline-group-header">
        <i class="fas fa-truck-medical"></i>
        Emergency Services
      </div>
      <div class="hotline-list">
        <div class="hotline-item">
          <div>
            <div class="hotline-name">National Emergency Hotline</div>
            <div class="hotline-sub">Police / Fire / Medical</div>
          </div>
          <a href="tel:911" class="hotline-number"><i class="fas fa-phone"></i> 911</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Marikina CDRRMO</div>
            <div class="hotline-sub">Disaster Risk Reduction</div>
          </div>
          <a href="tel:+6328646174" class="hotline-number"><i class="fas fa-phone"></i> (02) 864-6174</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Marikina Fire Station</div>
            <div class="hotline-sub">Bureau of Fire Protection</div>
          </div>
          <a href="tel:+6328646333" class="hotline-number"><i class="fas fa-phone"></i> (02) 864-6333</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Marikina City Hospital</div>
            <div class="hotline-sub">Emergency Medical Services</div>
          </div>
          <a href="tel:+6328647700" class="hotline-number"><i class="fas fa-phone"></i> (02) 864-7700</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Philippine Red Cross</div>
            <div class="hotline-sub">Medical Assistance</div>
          </div>
          <a href="tel:143" class="hotline-number"><i class="fas fa-phone"></i> 143</a>
        </div>
      </div>
    </div>

    <!-- Legal -->
    <div class="hotline-group legal reveal">
      <div class="hotline-group-header">
        <i class="fas fa-scale-balanced"></i>
        Legal &amp; Peace-Order
      </div>
      <div class="hotline-list">
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Marikina City Police</div>
            <div class="hotline-sub">Philippine National Police</div>
          </div>
          <a href="tel:+6328645555" class="hotline-number"><i class="fas fa-phone"></i> (02) 864-5555</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">NBI Hotline</div>
            <div class="hotline-sub">National Bureau of Investigation</div>
          </div>
          <a href="tel:523-8231" class="hotline-number"><i class="fas fa-phone"></i> 523-8231</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">BJMP</div>
            <div class="hotline-sub">Bureau of Jail Management</div>
          </div>
          <a href="tel:+6328646161" class="hotline-number"><i class="fas fa-phone"></i> (02) 864-6161</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">PAO Hotline</div>
            <div class="hotline-sub">Public Attorney's Office (Free Legal Aid)</div>
          </div>
          <a href="tel:1037" class="hotline-number"><i class="fas fa-phone"></i> 1037</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Barangay Hall</div>
            <div class="hotline-sub">Barangay Nangka Office</div>
          </div>
          <a href="tel:+6328XXXXXXX" class="hotline-number"><i class="fas fa-phone"></i> Inquire</a>
        </div>
      </div>
    </div>

    <!-- Health -->
    <div class="hotline-group health reveal">
      <div class="hotline-group-header">
        <i class="fas fa-heart-pulse"></i>
        Health &amp; Social Services
      </div>
      <div class="hotline-list">
        <div class="hotline-item">
          <div>
            <div class="hotline-name">DOH Health Hotline</div>
            <div class="hotline-sub">Department of Health</div>
          </div>
          <a href="tel:1555" class="hotline-number"><i class="fas fa-phone"></i> 1555</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">DSWD Hotline</div>
            <div class="hotline-sub">Social Welfare &amp; Development</div>
          </div>
          <a href="tel:931-8101" class="hotline-number"><i class="fas fa-phone"></i> 931-8101</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Crisis Line</div>
            <div class="hotline-sub">NCMH Mental Health Support</div>
          </div>
          <a href="tel:1553" class="hotline-number"><i class="fas fa-phone"></i> 1553</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">VAWC Hotline</div>
            <div class="hotline-sub">Violence Against Women &amp; Children</div>
          </div>
          <a href="tel:1343" class="hotline-number"><i class="fas fa-phone"></i> 1343</a>
        </div>
        <div class="hotline-item">
          <div>
            <div class="hotline-name">Bantay Bata</div>
            <div class="hotline-sub">Child Protection Hotline</div>
          </div>
          <a href="tel:163" class="hotline-number"><i class="fas fa-phone"></i> 163</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     SOCIAL MEDIA
     ============================================================ -->
<section class="social-section" id="social">
  <div class="reveal">
    <div class="section-eyebrow">Stay Connected</div>
    <h2 class="section-title">Follow Barangay Nangka</h2>
    <p class="section-lead">Get the latest announcements, events, and advisories directly from our official social media pages.</p>
  </div>

  <div class="social-links reveal">
    <a href="https://www.facebook.com/Barangay.Nangka.Official" target="_blank" class="social-card fb">
      <i class="fab fa-facebook-f"></i>
      <span>Facebook</span>
    </a>
    <a href="https://youtube.com" target="_blank" class="social-card yt">
      <i class="fab fa-youtube"></i>
      <span>YouTube</span>
    </a>
    <a href="https://twitter.com" target="_blank" class="social-card tw">
      <i class="fab fa-x-twitter"></i>
      <span>X / Twitter</span>
    </a>
    <a href="https://instagram.com" target="_blank" class="social-card ig">
      <i class="fab fa-instagram"></i>
      <span>Instagram</span>
    </a>
    <a href="https://t.me" target="_blank" class="social-card tg">
      <i class="fab fa-telegram"></i>
      <span>Telegram</span>
    </a>
  </div>

  <!-- Social embed windows -->
  <div class="social-embed-row reveal">
    <div class="social-embed-box">
      <div class="social-embed-header" style="--sc:#1877f2;">
        <i class="fab fa-facebook-f"></i>
        Official Facebook Page
      </div>
      <iframe src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2Freel%2F1432357244886530%2F&show_text=false&width=560&t=0" width="100%" height="315" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>
    </div>
    <div class="social-embed-box">
      <div class="social-embed-header" style="--sc:#ff0000;">
        <i class="fab fa-youtube"></i>
        YouTube Channel
      </div>
<iframe width="100%" height="315" src="https://www.youtube.com/embed/9jUTe1KO5Gg?si=Ustz6-aAoDiyMAAJ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
  </div>
</section>

<!-- ============================================================
     CONTACT FORM (Web3Forms)
     ============================================================ -->
<section class="contact-section" id="contact">
  <div class="contact-inner">
    <div class="contact-info reveal">
      <div class="section-eyebrow" style="color:var(--gold-light);"><span style="background:var(--gold-light);"></span> Contact Us</div>
      <h2 class="section-title light">Get in Touch</h2>
      <p class="section-lead light">Have questions, concerns, or feedback for the barangay? Send us a message and we'll get back to you as soon as possible.</p>

      <div class="contact-detail-list">
        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fas fa-location-dot"></i></div>
          <div class="contact-detail-text">
            <strong>Address</strong>
            <span>9 Old J.P. Rizal, Nangka, Marikina City, 1808 Metro Manila</span>
          </div>
        </div>
        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fas fa-phone"></i></div>
          <div class="contact-detail-text">
            <strong>Phone</strong>
            <span>(02) 8XXX-XXXX</span>
          </div>
        </div>
        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fas fa-envelope"></i></div>
          <div class="contact-detail-text">
            <strong>Email</strong>
            <span>barangaynangka@marikina.gov.ph</span>
          </div>
        </div>
        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fas fa-clock"></i></div>
          <div class="contact-detail-text">
            <strong>Office Hours</strong>
            <span>Monday – Friday, 8:00 AM – 5:00 PM</span>
          </div>
        </div>
      </div>
    </div>

    <div class="contact-form-card reveal">
      <h3><i class="fas fa-paper-plane"></i> Send a Message</h3>

      <!-- Web3Forms — replace YOUR_ACCESS_KEY with your actual key from web3forms.com -->
      <form id="contactForm" action="https://api.web3forms.com/submit" method="POST">
        <input type="hidden" name="access_key" value="4f39977f-8c90-4fd2-bef8-5afb62f3b789">
        <input type="hidden" name="subject" value="New Message - Barangay Nangka Website">
        <input type="hidden" name="from_name" value="Barangay Nangka Website">
        <input type="checkbox" name="botcheck" style="display:none;">

        <div class="form-row">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" placeholder="Juan dela Cruz" required>
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="phone" placeholder="09XX-XXX-XXXX">
          </div>
        </div>

        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
          <label>Concern Type</label>
          <select name="concern_type">
            <option value="">Select a concern</option>
            <option>Document Request Inquiry</option>
            <option>Complaint / Incident</option>
            <option>Community Suggestion</option>
            <option>Emergency Assistance</option>
            <option>Social Services</option>
            <option>Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Message *</label>
          <textarea name="message" placeholder="Write your message or concern here..." required></textarea>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
          <i class="fas fa-paper-plane"></i>
          Send Message
        </button>
      </form>

      <!-- Success State -->
      <div class="form-success" id="formSuccess">
        <i class="fas fa-circle-check"></i>
        <h4>Message Sent!</h4>
        <p>Thank you for reaching out to Barangay Nangka. We'll respond to your concern within 1–2 business days.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <a href="#" class="nav-brand" style="text-decoration:none;">
        <div class="nav-logo">
          <img src="assets/img/logo.png" alt="Barangay Nangka Logo">
        </div>
        <div class="nav-title">
          <span class="main">Barangay Nangka</span>
          <span class="sub">Marikina City</span>
        </div>
      </a>
      <p class="desc">Serving the community of Nangka, Marikina City with transparency, efficiency, and genuine care for every resident.</p>
      <p class="desc">Disclaimer: This is a personal project by Kyle Dominic Yap for demonstration purposes. It is not an official platform of Barangay Nangka. All data, documents, and financial records generated here are simulated and hold no legal validity.</p>
      <div class="footer-social-mini">
        <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
        <a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
        <a href="https://twitter.com" target="_blank"><i class="fab fa-x-twitter"></i></a>
        <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="https://t.me" target="_blank"><i class="fab fa-telegram"></i></a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#about">About Barangay Nangka</a></li>
        <li><a href="#services">Online Services</a></li>
        <li><a href="#location">Location &amp; Map</a></li>
        <li><a href="#hotlines">Hotlines</a></li>
        <li><a href="#contact">Contact Us</a></li>
        <li><a href="login.php">Login Here</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="login.php">Barangay Clearance</a></li>
        <li><a href="login.php">Business Clearance</a></li>
        <li><a href="login.php">Certificate of Residency</a></li>
        <li><a href="login.php">Indigency Certificate</a></li>
        <li><a href="login.php">Good Moral Certificate</a></li>
        <li><a href="login.php">File a Complaint</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <span>&copy; <?php echo date('Y'); ?> Barangay Nangka, Marikina City. All rights reserved.</span>
    <span>Powered by <a href="login.php">Barangay Digital Management System</a></span>
  </div>
</footer>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
// ---- NAVBAR SCROLL ----
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 60);
});

// ---- CAROUSEL ----
const track = document.getElementById('carouselTrack');
const slides = track.children;
const dotsContainer = document.getElementById('carouselDots');
let current = 0;
let autoTimer;

// Build dots
Array.from(slides).forEach((_, i) => {
  const dot = document.createElement('button');
  dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
  dot.addEventListener('click', () => goTo(i));
  dotsContainer.appendChild(dot);
});

function updateDots() {
  document.querySelectorAll('.carousel-dot').forEach((d, i) => {
    d.classList.toggle('active', i === current);
  });
}

function goTo(idx) {
  current = (idx + slides.length) % slides.length;
  track.style.transform = `translateX(-${current * 100}%)`;
  updateDots();
  resetTimer();
}

function next() { goTo(current + 1); }
function prev() { goTo(current - 1); }

document.getElementById('nextBtn').addEventListener('click', next);
document.getElementById('prevBtn').addEventListener('click', prev);

function resetTimer() {
  clearInterval(autoTimer);
  autoTimer = setInterval(next, 5000);
}

resetTimer();

// Touch swipe
let touchStartX = 0;
track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
track.addEventListener('touchend', e => {
  const diff = touchStartX - e.changedTouches[0].clientX;
  if (Math.abs(diff) > 50) diff > 0 ? next() : prev();
});

// ---- MOBILE NAV ----
const mobileNav = document.getElementById('mobileNav');
document.getElementById('hamburger').addEventListener('click', () => mobileNav.classList.add('open'));
document.getElementById('navClose').addEventListener('click', closeMobileNav);
document.getElementById('navOverlay').addEventListener('click', closeMobileNav);
function closeMobileNav() { mobileNav.classList.remove('open'); }

// ---- SCROLL REVEAL ----
const revealEls = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), i * 80);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });
revealEls.forEach(el => observer.observe(el));

// ---- WEB3FORMS CONTACT ----
document.getElementById('contactForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('submitBtn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
  btn.disabled = true;

  try {
    const res = await fetch('https://api.web3forms.com/submit', {
      method: 'POST',
      body: new FormData(this)
    });
    const data = await res.json();

    if (data.success) {
      document.getElementById('contactForm').style.display = 'none';
      document.getElementById('formSuccess').style.display = 'block';
    } else {
      throw new Error(data.message);
    }
  } catch (err) {
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
    btn.disabled = false;
    alert('Something went wrong. Please try again or contact us directly.');
  }
});
</script>

</body>
</html>