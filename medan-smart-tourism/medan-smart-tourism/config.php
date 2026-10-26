<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
$pdo = new PDO("mysql:host=localhost;dbname=medan_tiket;charset=utf8mb4","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
define('BASE', str_replace(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',str_replace('\\','/',__DIR__)));
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function rp($n){return 'Rp '.number_format($n,0,',','.');}
function user(){return $_SESSION['user']??null;}
function need($roles){$u=user();if(!$u||!in_array($u['role'],(array)$roles)){header('Location: '.BASE.'/login.php');exit;}}
function flash($m=null,$t='success'){if($m){$_SESSION['flash']=[$m,$t];return;}if(!empty($_SESSION['flash'])){[$m,$t]=$_SESSION['flash'];unset($_SESSION['flash']);
$icon=$t==='success'?'✅':($t==='danger'?'❌':($t==='warning'?'⚠️':'ℹ️'));
return "<div class='flash-alert flash-{$t}'><span class='flash-icon'>{$icon}</span><span>".e($m)."</span><button class='flash-close' onclick='this.parentElement.remove()'>×</button></div>";}
}
function thumb($d,$cls=''){
  if(!empty($d['gambar']))return '<img class="thumb '.$cls.'" src="'.BASE.'/uploads/'.e($d['gambar']).'" alt="'.e($d['nama']).'">';
  $g=[['#6366f1','#a855f7'],['#f59e0b','#ef4444'],['#06b6d4','#3b82f6'],['#ec4899','#f43f5e'],['#10b981','#14b8a6']][$d['id']%5];
  $emojis=['🏛️','🌴','🎭','🏖️','🎪','🗿','🌺','🎡'][$d['id']%8];
  return '<div class="thumb '.$cls.'" style="background:linear-gradient(135deg,'.$g[0].','.$g[1].')"><span style="font-size:3rem">'.$emojis.'</span></div>';
}
function go($u){header('Location: '.$u);exit;}
function statusBadge($s){
  $map=['pending'=>['⏳ Menunggu Bayar','badge-warning'],'paid'=>['✅ Lunas','badge-success'],'checked_in'=>['🎟️ Check-in','badge-info'],'cancelled'=>['❌ Batal','badge-danger']];
  $b=$map[$s]??[$s,'badge-secondary'];
  return "<span class='status-badge {$b[1]}'>{$b[0]}</span>";
}
function head($title){$u=user();?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> — Medan Smart Tourism</title>
<meta name="description" content="Pesan e-tiket wisata Medan, dapatkan nomor antrian digital, dan check-in cukup dengan QR Code.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{
  --bg:#0a0f1e;
  --bg2:#111827;
  --bg3:#1a2235;
  --surface:rgba(255,255,255,0.05);
  --surface-hover:rgba(255,255,255,0.09);
  --border:rgba(255,255,255,0.08);
  --primary:#6366f1;
  --primary-glow:rgba(99,102,241,0.35);
  --secondary:#a855f7;
  --accent:#f59e0b;
  --success:#10b981;
  --danger:#ef4444;
  --warning:#f59e0b;
  --info:#06b6d4;
  --text:#f1f5f9;
  --text-muted:#94a3b8;
  --radius:16px;
  --radius-sm:10px;
  --shadow:0 20px 60px rgba(0,0,0,0.5);
  --shadow-sm:0 4px 20px rgba(0,0,0,0.3);
  --transition:0.3s cubic-bezier(0.4,0,0.2,1);
}

*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--bg);
  color:var(--text);
  min-height:100vh;
  background-image:
    radial-gradient(ellipse 80% 50% at 20% -20%, rgba(99,102,241,0.15) 0%, transparent 60%),
    radial-gradient(ellipse 60% 40% at 80% 100%, rgba(168,85,247,0.12) 0%, transparent 60%);
}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:6px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--primary);border-radius:99px}

/* ── NAVBAR ── */
.navbar{
  background:rgba(10,15,30,0.85)!important;
  backdrop-filter:blur(20px);
  -webkit-backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);
  padding:14px 0;
  position:sticky;top:0;z-index:999;
}
.navbar-brand{
  font-weight:800;font-size:1.2rem;
  background:linear-gradient(135deg,#6366f1,#a855f7);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  letter-spacing:-0.3px;
}
.nav-pill{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 16px;border-radius:99px;font-size:0.82rem;font-weight:600;
  text-decoration:none;transition:var(--transition);border:1px solid transparent;
}
.nav-pill-outline{color:var(--text-muted);border-color:var(--border);}
.nav-pill-outline:hover{color:var(--text);background:var(--surface);border-color:rgba(99,102,241,0.4);}
.nav-pill-primary{background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff;border:none;}
.nav-pill-primary:hover{transform:translateY(-1px);box-shadow:0 4px 20px var(--primary-glow);color:#fff;}
.nav-pill-accent{background:rgba(245,158,11,0.15);color:#f59e0b;border-color:rgba(245,158,11,0.3);}
.nav-pill-accent:hover{background:rgba(245,158,11,0.25);color:#f59e0b;}
.user-chip{
  display:inline-flex;align-items:center;gap:8px;
  background:var(--surface);border:1px solid var(--border);
  padding:6px 14px;border-radius:99px;font-size:0.8rem;color:var(--text-muted);
}
.user-avatar{
  width:26px;height:26px;border-radius:50%;
  background:linear-gradient(135deg,var(--primary),var(--secondary));
  display:inline-flex;align-items:center;justify-content:center;
  font-size:0.7rem;font-weight:700;color:#fff;
}

/* ── CONTAINER ── */
.page-wrapper{max-width:1200px;margin:0 auto;padding:32px 20px 64px;}

/* ── HERO ── */
.hero{
  background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(168,85,247,0.15));
  border:1px solid rgba(99,102,241,0.3);
  border-radius:24px;padding:56px 48px;
  position:relative;overflow:hidden;margin-bottom:40px;
}
.hero::before{
  content:'';position:absolute;top:-60px;right:-60px;
  width:300px;height:300px;border-radius:50%;
  background:radial-gradient(circle,rgba(168,85,247,0.2) 0%,transparent 70%);
  pointer-events:none;
}
.hero::after{
  content:'';position:absolute;bottom:-80px;left:30%;
  width:200px;height:200px;border-radius:50%;
  background:radial-gradient(circle,rgba(99,102,241,0.15) 0%,transparent 70%);
  pointer-events:none;
}
.hero h1{
  font-size:2.6rem;font-weight:800;line-height:1.2;
  background:linear-gradient(135deg,#fff 0%,#c4b5fd 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-bottom:12px;
}
.hero p{color:rgba(255,255,255,0.7);font-size:1.05rem;max-width:560px;}
.hero-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(99,102,241,0.2);border:1px solid rgba(99,102,241,0.4);
  padding:5px 14px;border-radius:99px;font-size:0.78rem;font-weight:600;
  color:#a5b4fc;margin-bottom:16px;
}

/* ── SEARCH BOX ── */
.search-wrap{display:flex;gap:10px;max-width:500px;margin-top:24px;position:relative;z-index:1;}
.search-input{
  flex:1;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);
  color:#fff;padding:13px 20px;border-radius:99px;font-size:0.92rem;
  font-family:inherit;transition:var(--transition);outline:none;
}
.search-input::placeholder{color:rgba(255,255,255,0.4)}
.search-input:focus{background:rgba(255,255,255,0.12);border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);}
.btn-search{
  background:linear-gradient(135deg,#6366f1,#a855f7);
  color:#fff;border:none;padding:13px 28px;border-radius:99px;
  font-weight:700;font-size:0.9rem;cursor:pointer;transition:var(--transition);
  white-space:nowrap;
}
.btn-search:hover{transform:translateY(-2px);box-shadow:0 8px 25px var(--primary-glow);}

/* ── CARDS ── */
.card-dest{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  overflow:hidden;
  transition:var(--transition);
  cursor:pointer;
  display:flex;flex-direction:column;
}
.card-dest:hover{
  transform:translateY(-8px);
  border-color:rgba(99,102,241,0.5);
  box-shadow:0 20px 50px rgba(99,102,241,0.2);
  background:var(--surface-hover);
}
.thumb{
  height:200px;width:100%;object-fit:cover;
  display:flex;align-items:center;justify-content:center;
  position:relative;
}
.thumb-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to top,rgba(0,0,0,0.6) 0%,transparent 50%);
}
.card-body-custom{padding:20px;flex:1;display:flex;flex-direction:column;}
.card-body-custom h5{font-size:1rem;font-weight:700;margin-bottom:8px;color:var(--text);}
.card-body-custom p{font-size:0.82rem;color:var(--text-muted);flex:1;line-height:1.6;}
.card-meta{
  display:flex;align-items:center;gap:6px;
  font-size:0.78rem;color:var(--text-muted);margin:10px 0;
}
.card-meta span{
  background:rgba(255,255,255,0.06);
  padding:3px 10px;border-radius:99px;border:1px solid var(--border);
}
.card-footer-custom{
  display:flex;justify-content:space-between;align-items:center;
  padding-top:14px;border-top:1px solid var(--border);margin-top:auto;
}
.price-tag{font-size:1.1rem;font-weight:800;color:#a5b4fc;}

/* ── BUTTONS ── */
.btn-glow{
  background:linear-gradient(135deg,#6366f1,#a855f7);
  color:#fff;border:none;padding:10px 22px;border-radius:99px;
  font-weight:700;font-size:0.85rem;cursor:pointer;transition:var(--transition);
  text-decoration:none;display:inline-flex;align-items:center;gap:6px;
}
.btn-glow:hover{transform:translateY(-2px);box-shadow:0 8px 25px var(--primary-glow);color:#fff;}
.btn-glow-sm{padding:7px 16px;font-size:0.78rem;}
.btn-outline-glow{
  background:transparent;color:#a5b4fc;
  border:1px solid rgba(99,102,241,0.5);
  padding:10px 22px;border-radius:99px;font-weight:600;font-size:0.85rem;
  cursor:pointer;transition:var(--transition);text-decoration:none;
  display:inline-flex;align-items:center;gap:6px;
}
.btn-outline-glow:hover{background:rgba(99,102,241,0.15);color:#a5b4fc;border-color:var(--primary);}
.btn-success-custom{
  background:linear-gradient(135deg,#10b981,#059669);
  color:#fff;border:none;padding:10px 22px;border-radius:99px;
  font-weight:700;font-size:0.85rem;cursor:pointer;transition:var(--transition);
}
.btn-success-custom:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(16,185,129,0.4);}
.btn-danger-sm{
  background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);
  padding:6px 14px;border-radius:99px;font-size:0.78rem;font-weight:600;
  cursor:pointer;text-decoration:none;transition:var(--transition);display:inline-flex;align-items:center;
}
.btn-danger-sm:hover{background:rgba(239,68,68,0.3);color:#fca5a5;}
.btn-warning-sm{
  background:rgba(245,158,11,0.15);color:#fbbf24;border:1px solid rgba(245,158,11,0.3);
  padding:6px 14px;border-radius:99px;font-size:0.78rem;font-weight:600;
  cursor:pointer;text-decoration:none;transition:var(--transition);display:inline-flex;align-items:center;
}
.btn-warning-sm:hover{background:rgba(245,158,11,0.3);color:#fde68a;}

/* ── GLASS CARD ── */
.glass-card{
  background:rgba(255,255,255,0.04);
  border:1px solid var(--border);
  border-radius:var(--radius);
  backdrop-filter:blur(10px);
}
.glass-card-inner{padding:32px;}

/* ── STAT CARDS ── */
.stat-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius);padding:24px;
  transition:var(--transition);position:relative;overflow:hidden;
}
.stat-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,var(--c1,#6366f1),var(--c2,#a855f7));
}
.stat-card:hover{transform:translateY(-4px);border-color:rgba(99,102,241,0.4);}
.stat-label{font-size:0.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;}
.stat-value{font-size:1.7rem;font-weight:800;color:var(--text);}
.stat-icon{
  position:absolute;right:20px;top:20px;font-size:2rem;opacity:0.15;
}

/* ── SECTION TITLES ── */
.section-title{
  font-size:1.5rem;font-weight:800;margin-bottom:6px;
  background:linear-gradient(135deg,#fff,#c4b5fd);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.section-subtitle{color:var(--text-muted);font-size:0.9rem;margin-bottom:28px;}

/* ── TABLE ── */
.table-custom{width:100%;border-collapse:separate;border-spacing:0 8px;}
.table-custom thead tr{background:transparent;}
.table-custom thead th{
  color:var(--text-muted);font-size:0.75rem;font-weight:700;
  text-transform:uppercase;letter-spacing:0.06em;
  padding:8px 16px;border:none;
}
.table-custom tbody tr{
  background:var(--surface);
  border-radius:var(--radius-sm);
  transition:var(--transition);
}
.table-custom tbody tr:hover{background:var(--surface-hover);}
.table-custom tbody td{
  padding:14px 16px;color:var(--text);font-size:0.88rem;border:none;
  border-top:1px solid var(--border);border-bottom:1px solid var(--border);
}
.table-custom tbody td:first-child{border-left:1px solid var(--border);border-radius:var(--radius-sm) 0 0 var(--radius-sm);}
.table-custom tbody td:last-child{border-right:1px solid var(--border);border-radius:0 var(--radius-sm) var(--radius-sm) 0;}

/* ── STATUS BADGES ── */
.status-badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:4px 12px;border-radius:99px;font-size:0.75rem;font-weight:700;
}
.badge-success{background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);}
.badge-warning{background:rgba(245,158,11,0.15);color:#fbbf24;border:1px solid rgba(245,158,11,0.3);}
.badge-danger{background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);}
.badge-info{background:rgba(6,182,212,0.15);color:#22d3ee;border:1px solid rgba(6,182,212,0.3);}
.badge-secondary{background:rgba(148,163,184,0.15);color:#94a3b8;border:1px solid rgba(148,163,184,0.3);}

/* ── FORMS ── */
.form-group{margin-bottom:18px;}
.form-label-custom{display:block;font-size:0.82rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.04em;}
.form-control-custom{
  width:100%;background:rgba(255,255,255,0.06);
  border:1px solid var(--border);color:var(--text);
  padding:13px 18px;border-radius:var(--radius-sm);font-size:0.9rem;
  font-family:inherit;transition:var(--transition);outline:none;
}
.form-control-custom::placeholder{color:rgba(255,255,255,0.3)}
.form-control-custom:focus{background:rgba(255,255,255,0.09);border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);}
.form-control-custom option{background:#1a2235;color:#f1f5f9;}
.form-select-custom{
  width:100%;background:rgba(255,255,255,0.06) url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 14px center/16px 12px;
  border:1px solid var(--border);color:var(--text);
  padding:13px 40px 13px 18px;border-radius:var(--radius-sm);font-size:0.9rem;
  font-family:inherit;transition:var(--transition);outline:none;appearance:none;
}
.form-select-custom:focus{background-color:rgba(255,255,255,0.09);border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);}
.form-select-custom option{background:#1a2235;}

/* ── FLASH ALERTS ── */
.flash-alert{
  display:flex;align-items:center;gap:12px;
  padding:14px 20px;border-radius:var(--radius-sm);
  margin-bottom:20px;font-size:0.88rem;font-weight:600;
  animation:slideDown 0.4s cubic-bezier(0.34,1.56,0.64,1);
}
.flash-success{background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#34d399;}
.flash-danger{background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#f87171;}
.flash-warning{background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:#fbbf24;}
.flash-icon{font-size:1.1rem;}
.flash-close{
  margin-left:auto;background:none;border:none;color:inherit;
  font-size:1.2rem;cursor:pointer;opacity:0.6;line-height:1;padding:0 4px;
}
.flash-close:hover{opacity:1;}
@keyframes slideDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:translateY(0)}}

/* ── AUTH PAGES ── */
.auth-wrapper{
  min-height:calc(100vh - 80px);
  display:flex;align-items:center;justify-content:center;
  padding:40px 20px;
}
.auth-card{
  background:rgba(255,255,255,0.04);
  border:1px solid var(--border);border-radius:24px;
  padding:48px;max-width:440px;width:100%;
  backdrop-filter:blur(20px);
  box-shadow:0 30px 80px rgba(0,0,0,0.5);
}
.auth-logo{
  text-align:center;margin-bottom:32px;
}
.auth-logo-icon{
  width:64px;height:64px;border-radius:18px;
  background:linear-gradient(135deg,#6366f1,#a855f7);
  display:inline-flex;align-items:center;justify-content:center;
  font-size:2rem;margin-bottom:16px;
  box-shadow:0 8px 30px var(--primary-glow);
}
.auth-title{font-size:1.6rem;font-weight:800;margin-bottom:4px;}
.auth-sub{color:var(--text-muted);font-size:0.9rem;}
.auth-divider{
  display:flex;align-items:center;gap:16px;
  margin:20px 0;color:var(--text-muted);font-size:0.8rem;
}
.auth-divider::before,.auth-divider::after{
  content:'';flex:1;height:1px;background:var(--border);
}
.btn-auth{
  width:100%;background:linear-gradient(135deg,#6366f1,#a855f7);
  color:#fff;border:none;padding:15px;border-radius:var(--radius-sm);
  font-size:1rem;font-weight:700;cursor:pointer;transition:var(--transition);
  font-family:inherit;
}
.btn-auth:hover{transform:translateY(-2px);box-shadow:0 10px 35px var(--primary-glow);}

/* ── TICKET CARD ── */
.ticket-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:24px;overflow:hidden;max-width:480px;margin:0 auto;
  box-shadow:var(--shadow);
}
.ticket-header{
  background:linear-gradient(135deg,#6366f1,#a855f7);
  padding:28px 32px;text-align:center;
}
.ticket-header h3{font-size:1.1rem;font-weight:700;margin:0;color:#fff;}
.ticket-body{padding:32px;}
.queue-number{
  background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(168,85,247,0.2));
  border:2px solid rgba(99,102,241,0.4);border-radius:18px;
  padding:20px;text-align:center;margin:24px 0;
}
.queue-label{font-size:0.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;}
.queue-num{font-size:4rem;font-weight:900;line-height:1;
  background:linear-gradient(135deg,#a5b4fc,#c084fc);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.ticket-dashed{
  border-top:2px dashed var(--border);margin:20px 0;
  position:relative;
}
.ticket-dashed::before,.ticket-dashed::after{
  content:'';position:absolute;top:-10px;
  width:20px;height:20px;border-radius:50%;background:var(--bg);
}
.ticket-dashed::before{left:-30px;}
.ticket-dashed::after{right:-30px;}
.ticket-info-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:8px 0;border-bottom:1px solid var(--border);font-size:0.87rem;
}
.ticket-info-row:last-child{border-bottom:none;}
.ticket-info-label{color:var(--text-muted);}
.ticket-info-value{font-weight:700;text-align:right;}
#qr{display:flex;justify-content:center;margin:20px 0;}
#qr img,#qr canvas{border-radius:12px;padding:12px;background:#fff;}

/* ── PAYMENT PAGE ── */
.payment-method{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-sm);padding:16px 20px;
  display:flex;align-items:center;gap:14px;cursor:pointer;
  transition:var(--transition);margin-bottom:10px;
}
.payment-method:hover{border-color:var(--primary);background:var(--surface-hover);}
.payment-method input[type=radio]{accent-color:var(--primary);}
.payment-method-icon{
  width:40px;height:40px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:1.3rem;
}
.payment-method-text{flex:1;}
.payment-method-name{font-weight:700;font-size:0.9rem;}
.payment-method-desc{font-size:0.75rem;color:var(--text-muted);}
.total-box{
  background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(168,85,247,0.15));
  border:1px solid rgba(99,102,241,0.3);border-radius:var(--radius-sm);
  padding:20px 24px;margin-bottom:24px;
}
.total-amount{font-size:2rem;font-weight:900;
  background:linear-gradient(135deg,#a5b4fc,#c084fc);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}

/* ── SCAN PAGE ── */
.scan-wrapper{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius);overflow:hidden;
}
#reader{border-radius:var(--radius)!important;}

/* ── ADMIN SIDEBAR / NAV ── */
.admin-nav{
  display:flex;gap:8px;margin-bottom:28px;flex-wrap:wrap;
}
.admin-nav a{
  display:inline-flex;align-items:center;gap:8px;
  padding:10px 20px;border-radius:var(--radius-sm);font-size:0.85rem;font-weight:600;
  text-decoration:none;transition:var(--transition);color:var(--text-muted);
  border:1px solid var(--border);background:var(--surface);
}
.admin-nav a:hover,.admin-nav a.active{
  background:rgba(99,102,241,0.15);color:#a5b4fc;border-color:rgba(99,102,241,0.4);
}

/* ── MINI THUMB ── */
.mini-thumb{
  width:48px;height:48px;border-radius:10px;overflow:hidden;
  display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;
}
.mini-thumb img,.mini-thumb .thumb{width:100%;height:100%;font-size:1.2rem;}

/* ── EMPTY STATE ── */
.empty-state{
  text-align:center;padding:80px 20px;color:var(--text-muted);
}
.empty-state-icon{font-size:4rem;margin-bottom:16px;opacity:0.3;}
.empty-state p{font-size:1rem;}

/* ── FOOTER ── */
footer{
  text-align:center;padding:28px;
  color:var(--text-muted);font-size:0.8rem;
  border-top:1px solid var(--border);
  margin-top:auto;
}
footer span{
  background:linear-gradient(135deg,#6366f1,#a855f7);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:700;
}

/* ── ANIM ── */
@keyframes fadeInUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
.fade-in{animation:fadeInUp 0.5s ease both;}
.fade-in-1{animation-delay:0.1s}.fade-in-2{animation-delay:0.2s}.fade-in-3{animation-delay:0.3s}.fade-in-4{animation-delay:0.4s}

/* ── RESPONSIVE ── */
@media(max-width:768px){
  .hero{padding:36px 24px;}
  .hero h1{font-size:1.8rem;}
  .auth-card{padding:32px 24px;}
  .glass-card-inner{padding:24px;}
}

/* ── PRINT ── */
@media print{
  nav,footer,.no-print{display:none!important}
  body{background:#fff;color:#000}
  .ticket-card{border:1px solid #ccc;box-shadow:none;}
  .queue-num{-webkit-text-fill-color:#6366f1;}
}

/* ── BOOTSTRAP OVERRIDES ── */
.alert{background:var(--surface);border:1px solid var(--border);color:var(--text);border-radius:var(--radius-sm);}
.alert-success{background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.3);color:#34d399;}
.alert-danger{background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.3);color:#f87171;}
.alert-warning{background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.3);color:#fbbf24;}
.form-control,.form-select{background:rgba(255,255,255,0.06);border:1px solid var(--border);color:var(--text);border-radius:var(--radius-sm);}
.form-control:focus,.form-select:focus{background:rgba(255,255,255,0.09);border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);color:var(--text);}
.form-control::placeholder{color:rgba(255,255,255,0.3)}
.form-control option,.form-select option{background:#1a2235;}
.table{color:var(--text);}
.card{background:var(--surface);border:1px solid var(--border);color:var(--text);border-radius:var(--radius);}
.card-header{background:rgba(255,255,255,0.05);border-color:var(--border);}
.table-striped>tbody>tr:nth-of-type(odd)>*{background:rgba(255,255,255,0.03);color:var(--text);}
.btn-success{background:linear-gradient(135deg,#10b981,#059669)!important;border:none!important;border-radius:99px!important;font-weight:700!important;}
.btn-success:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(16,185,129,0.35)!important;}
.btn-outline-success{color:#34d399!important;border-color:rgba(16,185,129,0.4)!important;border-radius:99px!important;}
.btn-outline-success:hover{background:rgba(16,185,129,0.15)!important;color:#34d399!important;}
.btn-warning{background:linear-gradient(135deg,#f59e0b,#d97706)!important;border:none!important;border-radius:99px!important;font-weight:700!important;}
.btn-danger{background:linear-gradient(135deg,#ef4444,#dc2626)!important;border:none!important;border-radius:99px!important;font-weight:700!important;}
.btn-outline-danger{color:#f87171!important;border-color:rgba(239,68,68,0.4)!important;border-radius:99px!important;}
.btn-secondary{background:rgba(148,163,184,0.2)!important;border:1px solid var(--border)!important;color:var(--text)!important;border-radius:99px!important;}
</style>
</head>
<body>
<nav class="navbar">
  <div class="container">
    <a class="navbar-brand" href="<?=BASE?>/index.php">🏛️ Medan Smart Tourism</a>
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <a class="nav-pill nav-pill-outline" href="<?=BASE?>/index.php">🗺️ Wisata</a>
      <?php if($u): ?>
        <?php if($u['role']==='user'):?>
          <a class="nav-pill nav-pill-outline" href="<?=BASE?>/tiket.php">🎟️ Tiket Saya</a>
        <?php endif;?>
        <?php if($u['role']==='admin'):?>
          <a class="nav-pill nav-pill-accent" href="<?=BASE?>/admin/index.php">⚙️ Admin</a>
        <?php endif;?>
        <?php if(in_array($u['role'],['admin','petugas'])):?>
          <a class="nav-pill nav-pill-accent" href="<?=BASE?>/petugas/scan.php">📷 Scan</a>
        <?php endif;?>
        <div class="user-chip">
          <span class="user-avatar"><?=strtoupper(substr($u['nama'],0,1))?></span>
          <span><?=e($u['nama'])?></span>
        </div>
        <a class="nav-pill nav-pill-outline" href="<?=BASE?>/logout.php">Keluar</a>
      <?php else:?>
        <a class="nav-pill nav-pill-outline" href="<?=BASE?>/login.php">Masuk</a>
        <a class="nav-pill nav-pill-primary" href="<?=BASE?>/register.php">Daftar Gratis</a>
      <?php endif;?>
    </div>
  </div>
</nav>
<div class="page-wrapper">
<?=flash()?>
<?php }
function foot(){echo '<p></p></div><footer><span>Medan Smart Tourism</span> · E-Ticketing & E-Queue · © 2026</footer></body></html>';}
?>
