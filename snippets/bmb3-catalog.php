<?php
/**
 * BMB3 — קטלוג רכבים  v2 (כהה+ירוק: רקע #0E0F13, כרטיסי רכב לבנים, אקצנט ירוק, גופן Assistant)
 * shortcode: [bmb3_catalog]
 *
 * התקנה ב-WPCode: New Snippet → PHP Snippet → הדבק הכל → Auto Insert / Run Everywhere → Active → Save.
 * הצבה: עמוד רגיל חדש בשם "קטלוג" (או בתבנית Archive) → ווידג'ט Shortcode → [bmb3_catalog]
 *
 * מציג את כל הרכבים שפורסמו. בלי מחיר. כל כרטיס מוביל לדף הרכב.
 * ACF: manufacturer, car_model, year, mileage, status, image_1.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'bmb3_catalog_pmt' ) ) {
	function bmb3_catalog_pmt( $P, $annual, $n ) {
		$r = $annual / 100 / 12;
		if ( abs( $r ) < 1e-9 ) return $P / $n;
		return $P * $r / ( 1 - pow( 1 + $r, -$n ) );
	}
}

if ( ! function_exists( 'bmb3_img_url' ) ) {
	function bmb3_img_url( $val ) {
		if ( empty( $val ) ) return '';
		if ( is_array( $val ) ) return isset( $val['url'] ) ? $val['url'] : '';
		if ( is_numeric( $val ) ) return wp_get_attachment_image_url( (int) $val, 'medium_large' );
		return is_string( $val ) ? $val : '';
	}
}

function bmb3_catalog_shortcode() {
	$q = new WP_Query( array(
		'post_type'      => 'car',
		'post_status'    => 'publish',
		'posts_per_page' => 200,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	$cards = '';
	$manufacturers = array();
	$years = array();
	$count = 0;

	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$pid = get_the_ID();
			$manufacturer = trim( (string) get_field( 'manufacturer', $pid ) );
			$model        = trim( (string) get_field( 'car_model', $pid ) );
			$year         = trim( (string) get_field( 'year', $pid ) );
			$mileage      = get_field( 'mileage', $pid );
			$status       = trim( (string) get_field( 'status', $pid ) );
			$price        = get_field( 'price', $pid );
			$img          = esc_url( bmb3_img_url( get_field( 'image_1', $pid ) ) );
			$is_sold      = ( $status === 'sold' || $status === 'נמכר' );
			if ( $is_sold ) continue;
			$price_fmt    = ( $price !== '' && $price !== null ) ? number_format( (float) $price ) : '';
			$deal_note = trim( (string) get_field( 'deal_note', $pid ) );

			// חישוב טווח החזר חודשי — אותם פרמטרים בדיוק כמו bmb3_finance
			$monthly_low  = '';
			$monthly_high = '';
			if ( $price !== '' && $price !== null && (float) $price > 0 ) {
				$p   = (float) $price;
				$yr  = (int) $year;
				$rmin = 5.85;
				if ( $yr >= 2023 )     { $rmax = 9;  $maxP = 100; }
				elseif ( $yr >= 2022 ) { $rmax = 9;  $maxP = 84;  }
				elseif ( $yr >= 2021 ) { $rmax = 10; $maxP = 84;  }
				elseif ( $yr >= 2020 ) { $rmax = 10; $maxP = 72;  }
				elseif ( $yr >= 2019 ) { $rmax = 11; $maxP = 72;  }
				else                   { $rmax = 14; $maxP = 60;  }
				$monthly_low  = (int) round( bmb3_catalog_pmt( $p, $rmin, $maxP ) );
				$monthly_high = (int) round( bmb3_catalog_pmt( $p, $rmax, $maxP ) );
			}

			$name = trim( $manufacturer . ' ' . $model );
			if ( $name === '' ) $name = get_the_title();
			$mileage_fmt = ( $mileage !== '' && $mileage !== null ) ? number_format( (float) $mileage ) : '';
			if ( $manufacturer !== '' ) $manufacturers[ $manufacturer ] = isset( $manufacturers[ $manufacturer ] ) ? $manufacturers[ $manufacturer ] + 1 : 1;
			if ( $year !== '' ) $years[ $year ] = 1;
			$count++;

			$meta_bits = array();
			if ( $year ) $meta_bits[] = esc_html( $year );
			if ( $mileage_fmt ) $meta_bits[] = esc_html( $mileage_fmt ) . ' ק"מ';
			$meta_line = implode( ' · ', $meta_bits );

			ob_start(); ?>
			<a class="bmb3-cc<?php echo $is_sold ? ' sold' : ''; ?>" href="<?php echo esc_url( get_permalink() ); ?>"
				data-mf="<?php echo esc_attr( $manufacturer ); ?>"
				data-year="<?php echo esc_attr( $year ); ?>"
				data-km="<?php echo esc_attr( (int) $mileage ); ?>"
				data-status="<?php echo $is_sold ? 'sold' : 'avail'; ?>"
				data-type="<?php echo esc_attr( strtolower( trim( (string) get_field( 'body_type', $pid ) ) ) ); ?>"
				data-s="<?php echo esc_attr( mb_strtolower( $name ) ); ?>">
				<div class="bmb3-cc-img">
					<span class="bmb3-cc-badge <?php echo $is_sold ? 'sold' : 'avail'; ?>"><?php echo $is_sold ? 'נמכר' : 'זמין'; ?></span>
					<?php if ( $deal_note !== '' ) : ?>
	<span class="bmb3-cc-deal"><span class="bmb3-cc-deal-icon">★</span><span class="bmb3-cc-deal-text"><?php echo esc_html( $deal_note ); ?></span></span>
<?php endif; ?>
					<?php if ( $img ) : ?>
						<img src="<?php echo $img; ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
					<?php else : ?>
						<div class="bmb3-cc-noimg"><svg viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1"><path d="M3 13l2-5a3 3 0 0 1 2.8-2h8.4A3 3 0 0 1 19 8l2 5v5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-1H7v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><circle cx="7.5" cy="14.5" r="1.2"/><circle cx="16.5" cy="14.5" r="1.2"/></svg></div>
					<?php endif; ?>
				</div>
				<div class="bmb3-cc-body">
					<h3 class="bmb3-cc-name"><?php echo esc_html( $name ); ?></h3>
					<?php if ( $meta_line ) : ?><div class="bmb3-cc-meta"><?php echo $meta_line; ?></div><?php endif; ?>
					<?php if ( $price_fmt ) : ?><div class="bmb3-cc-price"><span class="t">מחירון</span><span class="n"><?php echo esc_html( $price_fmt ); ?> ₪</span></div><?php endif; ?>
					<?php if ( $monthly_low && $monthly_high ) : ?>
					<div class="bmb3-cc-finance">
						<div class="bmb3-cc-finance-cap">החזר חודשי משוער</div>
						<div class="bmb3-cc-finance-main"><?php echo number_format( $monthly_low ) . ' ₪ - ' . number_format( $monthly_high ); ?> ₪</div>
						<div class="bmb3-cc-finance-sub">עד - החל מ לחודש</div>
					</div>
					<?php endif; ?>
					<span class="bmb3-cc-view">לצפייה ברכב <span aria-hidden="true">←</span></span>
				</div>
			</a>
			<?php
			$cards .= ob_get_clean();
		}
	}
	wp_reset_postdata();

	krsort( $years );
	ksort( $manufacturers );

	$mf_sorted = $manufacturers;
	arsort( $mf_sorted );
	$brand_chips = '';
	foreach ( $mf_sorted as $mf_name => $mf_n ) {
		$brand_chips .= '<button type="button" class="bmb3-brand" data-mf="' . esc_attr( $mf_name ) . '">'
			. esc_html( $mf_name ) . '<span class="bmb3-brand-n">' . (int) $mf_n . '</span></button>';
	}

	ob_start(); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;500;600;700;800&display=swap">

	<div class="bmb3-cat" dir="rtl">
		<div class="bmb3-cat-head">
			<h2>הקטלוג שלנו</h2>
			<p>הסופר של הרכבים — כל הדגמים, מעודכן בזמן אמת.</p>
		</div>

		<?php if ( $brand_chips ) : ?>
		<div class="bmb3-brands-wrap">
			<div class="bmb3-brands-head">דפדוף לפי יצרן</div>
			<div class="bmb3-brands" id="bmb3-brands">
				<button type="button" class="bmb3-brand is-active" data-mf="">הכל</button>
				<?php echo $brand_chips; ?>
			</div>
		</div>
		<?php endif; ?>

		<div class="bmb3-filters">
			<div class="f search"><input type="text" id="bmb3f-search" placeholder="חיפוש לפי יצרן / דגם..."></div>
			<div class="f"><select id="bmb3f-mf"><option value="">כל היצרנים</option>
				<?php foreach ( array_keys( $manufacturers ) as $m ) : ?><option value="<?php echo esc_attr( $m ); ?>"><?php echo esc_html( $m ); ?></option><?php endforeach; ?>
			</select></div>
			<div class="f"><select id="bmb3f-year"><option value="">כל השנים</option>
				<?php foreach ( array_keys( $years ) as $y ) : ?><option value="<?php echo esc_attr( $y ); ?>"><?php echo esc_html( $y ); ?></option><?php endforeach; ?>
			</select></div>
			<div class="f"><select id="bmb3f-km"><option value="">כל הקילומטראז'</option>
				<option value="50000">עד 50,000</option><option value="100000">עד 100,000</option>
				<option value="150000">עד 150,000</option><option value="200000">עד 200,000</option>
			</select></div>
			<div class="bmb3-count"><span id="bmb3f-count"><?php echo (int) $count; ?></span> רכבים</div>
		</div>

		<div class="bmb3-grid" id="bmb3-grid"><?php echo $cards; ?></div>
		<div class="bmb3-empty-state" id="bmb3-noresults" hidden>לא נמצאו רכבים התואמים לסינון.</div>
	</div>

	<style>
	.elementor-section:has(.bmb3-cat),
.elementor-container:has(.bmb3-cat),
.elementor-column:has(.bmb3-cat),
.elementor-widget-wrap:has(.bmb3-cat),
.elementor-widget:has(.bmb3-cat),
.elementor-widget-container:has(.bmb3-cat) {
	background-color: #FAFAFA !important;
	padding-top: 0 !important;
	padding-bottom: 0 !important;
	margin-top: 0 !important;
	margin-bottom: 0 !important;
}
.bmb3-cc-deal{
	position:absolute; inset-block-start:12px; inset-inline-end:12px; z-index:2;
	display:inline-flex; align-items:center; gap:6px;
	font-size:11px; font-weight:800; letter-spacing:.04em;
	padding:7px 11px; border-radius:999px; color:#0c1408;
	background:linear-gradient(135deg,var(--green-l),var(--green-d));
	box-shadow:0 4px 16px -2px rgba(95,190,60,.55);
	backdrop-filter:blur(8px);
	max-width:42px; overflow:hidden; white-space:nowrap;
	transition:max-width .45s cubic-bezier(.2,.8,.2,1), box-shadow .3s;
}
.bmb3-cc-deal-icon{flex:0 0 auto; line-height:1; font-size:19px; animation:bmb3-deal-shake 2.4s ease-in-out infinite;}
.bmb3-cc-deal-text{flex:0 0 auto;}
@keyframes bmb3-deal-shake{
	0%,100%{transform:rotate(0deg) scale(1);}
	10%{transform:rotate(-10deg) scale(1.08);}
	20%{transform:rotate(10deg) scale(1.08);}
	30%{transform:rotate(-6deg);}
	40%{transform:rotate(5deg);}
	50%{transform:rotate(0deg) scale(1);}
}
@media (hover:hover){
	.bmb3-cc-deal:hover{max-width:min(220px,calc(100% - 24px)); box-shadow:0 4px 22px 2px rgba(95,190,60,.85);}
}
@media (hover:none){
	.bmb3-cc-deal{animation:bmb3-deal-cycle 6s ease-in-out infinite;}
}
@keyframes bmb3-deal-cycle{
	0%,55%{max-width:42px; box-shadow:0 4px 16px -2px rgba(95,190,60,.55);}
	70%,90%{max-width:min(220px,calc(100% - 24px)); box-shadow:0 4px 22px 2px rgba(95,190,60,.85);}
	100%{max-width:42px; box-shadow:0 4px 16px -2px rgba(95,190,60,.55);}
}
@media(prefers-reduced-motion:reduce){
	.bmb3-cc-deal, .bmb3-cc-deal-icon{animation:none !important; max-width:none;}
}
	.bmb3-cat{
		--bg:#FAFAFA; --card:#FFFFFF; --ink:#15202B; --muted:#5B6772; --muted2:#5C6B7A;
		--line:rgba(21,32,43,.10); --line-card:#E7ECF1; --tile:#F5F8FA;
		--green:#5FBE3C; --green-d:#46992A; --green-l:#86D766;
		font-family:'Assistant',sans-serif; color:var(--ink); direction:rtl; box-sizing:border-box;
		padding:46px clamp(16px,4vw,56px); border-radius:24px;
		background:radial-gradient(900px 500px at 85% -8%,rgba(70,153,42,.06),transparent 60%),var(--bg);
		border:1px solid var(--line);
	}
	.bmb3-cat *{box-sizing:border-box; font-family:'Assistant',sans-serif;}
	.bmb3-cat [hidden]{display:none !important;}
	.bmb3-cat-head{text-align:center; margin-bottom:30px;}
	.bmb3-cat-head h2{font-weight:800; font-size:clamp(28px,3.4vw,42px); margin:0 0 8px; color:var(--ink);}
	.bmb3-cat-head p{color:var(--muted); font-weight:500; letter-spacing:.02em; margin:0;}

	.bmb3-filters{display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:center;
		padding:16px; margin-bottom:30px; border:1px solid var(--line); border-radius:16px; background:#FFFFFF; box-shadow:0 18px 40px -30px rgba(21,32,43,.25);}
	.bmb3-filters .f{flex:0 1 auto;}
	.bmb3-filters .search{flex:1 1 240px; min-width:200px;}
	.bmb3-filters input[type=text], .bmb3-filters select{
		width:100%; background:#F5F8FA; border:1px solid var(--line); border-radius:10px; color:var(--ink);
		padding:11px 14px; font-family:inherit; font-size:14px; outline:none; transition:border-color .2s;}
	.bmb3-filters input:focus, .bmb3-filters select:focus{border-color:var(--green);}
	.bmb3-filters select{cursor:pointer; min-width:140px;}
	.bmb3-filters .toggle{display:inline-flex; align-items:center; gap:8px; color:var(--muted); font-size:14px; font-weight:500; cursor:pointer; padding:0 4px;}
	.bmb3-filters .toggle input{accent-color:var(--green); width:16px; height:16px; cursor:pointer;}
	.bmb3-count{color:var(--green-l); font-weight:600; letter-spacing:.02em; font-size:14px; margin-inline-start:auto;}
	.bmb3-count #bmb3f-count{font-weight:800; font-size:18px; color:var(--ink);}

	.bmb3-brands-wrap{margin-bottom:22px;}
	.bmb3-brands-head{text-align:center; color:var(--muted); font-weight:600; letter-spacing:.1em; font-size:13px; text-transform:uppercase; margin-bottom:14px;}
	.bmb3-brands{display:flex; flex-wrap:wrap; gap:10px; justify-content:center;}
	.bmb3-brand{display:inline-flex; align-items:center; gap:8px; font-family:inherit; font-size:14px; font-weight:600; color:var(--ink); cursor:pointer;
		padding:9px 16px; border-radius:999px; border:1px solid var(--line); background:#FFFFFF; transition:border-color .2s, color .2s, background .2s;}
	.bmb3-brand:hover{border-color:rgba(70,153,42,.4); color:var(--green-d);}
	.bmb3-brand.is-active{color:#0c1408; background:linear-gradient(135deg,var(--green-l),var(--green-d)); border-color:transparent;}
	.bmb3-brand-n{font-size:11px; font-weight:700; opacity:.7; background:rgba(21,32,43,.08); padding:1px 7px; border-radius:999px;}
	.bmb3-brand.is-active .bmb3-brand-n{background:rgba(0,0,0,.18); opacity:.85;}

	.bmb3-grid{display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:22px;}
	.bmb3-cc{display:flex; flex-direction:column; text-decoration:none; color:var(--ink); border-radius:16px; overflow:hidden;
		background:var(--card); border:1px solid var(--line-card); box-shadow:0 18px 40px -28px rgba(21,32,43,.18); transition:transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s;}
	.bmb3-cc:hover{transform:translateY(-6px); box-shadow:0 30px 50px -28px rgba(21,32,43,.25);}
	.bmb3-cc-img{position:relative; aspect-ratio:16/11; overflow:hidden;
		background:linear-gradient(110deg, var(--tile) 8%, #E4E9EE 18%, var(--tile) 33%);
		background-size:200% 100%; animation:bmb3-shimmer 1.6s linear infinite;}
	.bmb3-cc-img.bmb3-loaded{animation:none; background:var(--tile);}
	.bmb3-cc-img img{width:100%; height:100%; object-fit:cover; opacity:0; filter:blur(16px);
		transition:transform .8s cubic-bezier(.2,.8,.2,1), opacity .5s ease, filter .6s ease;}
	.bmb3-cc-img img.bmb3-loaded{opacity:1; filter:blur(0);}
	.bmb3-cc:hover .bmb3-cc-img img{transform:scale(1.07);}
	@keyframes bmb3-shimmer{0%{background-position:200% 0;} 100%{background-position:-200% 0;}}
	@media(prefers-reduced-motion:reduce){
		.bmb3-cc-img{animation:none;}
		.bmb3-cc-img img{transition:none; opacity:1; filter:none;}
	}
	.bmb3-cc-noimg{width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--muted);}
	.bmb3-cc-badge{position:absolute; inset-block-start:12px; inset-inline-start:12px; z-index:2; font-size:10px; font-weight:800; letter-spacing:.14em; padding:5px 11px; border-radius:999px; backdrop-filter:blur(8px);}
	.bmb3-cc-badge.avail{color:#0c1408; background:linear-gradient(135deg,var(--green-l),var(--green-d));}
	.bmb3-cc-badge.sold{color:#fff; background:rgba(14,15,19,.7); border:1px solid rgba(255,255,255,.2);}
	.bmb3-cc.sold .bmb3-cc-img img{filter:grayscale(.6) brightness(.85);}
	.bmb3-cc-body{padding:18px 18px 20px;}
	.bmb3-cc-name{font-weight:800; font-size:19px; margin:0 0 6px; color:var(--ink);}
	.bmb3-cc-meta{color:var(--muted2); font-weight:500; font-size:13.5px; letter-spacing:.02em; margin-bottom:14px;}
	.bmb3-cc-price{display:flex; align-items:baseline; justify-content:space-between; gap:10px; padding:10px 0 10px; border-top:1px solid var(--line-card);}
	.bmb3-cc-price .t{color:var(--muted2); font-weight:500; font-size:12.5px; letter-spacing:.02em;}
	.bmb3-cc-price .n{font-weight:800; font-size:19px; color:var(--ink);}
	.bmb3-cc-finance{background:#0E0F13; border-radius:11px; padding:10px 13px 11px; margin-bottom:12px; text-align:center;}
	.bmb3-cc-finance-cap{font-size:11px; color:#9AA3B0; margin-bottom:3px; font-weight:500;}
	.bmb3-cc-finance-main{font-size:16px; font-weight:800; color:#86D766; direction:ltr; unicode-bidi:embed; line-height:1.25;}
	.bmb3-cc-finance-sub{font-size:10.5px; color:#6f7787; margin-top:3px;}
	.bmb3-cc-view{display:inline-flex; align-items:center; gap:6px; color:var(--green-d); font-weight:700; font-size:14px; transition:gap .25s;}
	.bmb3-cc:hover .bmb3-cc-view{gap:11px;}
	.bmb3-empty-state{text-align:center; color:var(--muted); font-weight:500; padding:50px 0; letter-spacing:.02em;}

	@media (max-width:620px){.bmb3-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:14px;}
		.bmb3-cc-name{font-size:17px;} .bmb3-count{margin-inline-start:0; width:100%; text-align:center;}}
.bmb3-cc, .bmb3-fx-card{
	opacity:0; transform:translateY(24px);
	transition:opacity .6s ease, transform .6s cubic-bezier(.2,.8,.2,1);
}
.bmb3-cc.bmb3-in, .bmb3-fx-card.bmb3-in{
	opacity:1; transform:translateY(0);
}
@media(prefers-reduced-motion:reduce){
	.bmb3-cc, .bmb3-fx-card{opacity:1; transform:none; transition:none;}
}
</style>

	<script>
	(function(){
		var grid=document.getElementById('bmb3-grid'); if(!grid) return;
		var cards=[].slice.call(grid.querySelectorAll('.bmb3-cc'));
		grid.querySelectorAll('.bmb3-cc-img').forEach(function(wrap){
			var img=wrap.querySelector('img');
			if(!img){ wrap.classList.add('bmb3-loaded'); return; }
			function done(){ img.classList.add('bmb3-loaded'); wrap.classList.add('bmb3-loaded'); }
			if(img.complete && img.naturalWidth>0){ done(); } else { img.addEventListener('load',done); img.addEventListener('error',done); }
		});
		cards.forEach(function(c,i){ c.style.transitionDelay=((i%4)*70)+'ms'; });
if('IntersectionObserver' in window){
	var fxIo=new IntersectionObserver(function(entries){
		entries.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add('bmb3-in'); fxIo.unobserve(en.target); } });
	},{threshold:0.15, rootMargin:'0px 0px -40px 0px'});
	cards.forEach(function(c){ fxIo.observe(c); });
} else {
	cards.forEach(function(c){ c.classList.add('bmb3-in'); });
}
		var urlType=((new URLSearchParams(window.location.search)).get('type')||'').trim().toLowerCase();
		var s=document.getElementById('bmb3f-search'), mf=document.getElementById('bmb3f-mf'),
			yr=document.getElementById('bmb3f-year'), km=document.getElementById('bmb3f-km'),
			cnt=document.getElementById('bmb3f-count'),
			none=document.getElementById('bmb3-noresults');
		function apply(){
			var sv=(s.value||'').trim().toLowerCase(), mv=mf.value, yv=yr.value, kv=km.value?parseInt(km.value,10):0, shown=0;
			cards.forEach(function(c){
				var ok=true;
				if(sv && c.getAttribute('data-s').indexOf(sv)===-1) ok=false;
				if(mv && c.getAttribute('data-mf')!==mv) ok=false;
				if(urlType && (c.getAttribute('data-type')||'')!==urlType) ok=false;
				if(yv && c.getAttribute('data-year')!==yv) ok=false;
				if(kv && parseInt(c.getAttribute('data-km')||'0',10)>kv) ok=false;
				c.style.display=ok?'':'none'; if(ok) shown++;
			});
			if(cnt) cnt.textContent=shown;
			if(none) none.hidden=(shown>0);
		}
		[s,mf,yr,km].forEach(function(el){ if(el){el.addEventListener('input',apply); el.addEventListener('change',apply);} });

		var brands=document.getElementById('bmb3-brands');
		if(brands){
			brands.addEventListener('click',function(e){
				var b=e.target.closest('.bmb3-brand'); if(!b) return;
				var v=b.getAttribute('data-mf')||'';
				if(mf) mf.value=v;
				brands.querySelectorAll('.bmb3-brand').forEach(function(x){x.classList.remove('is-active');});
				b.classList.add('is-active');
				apply();
				grid.scrollIntoView({behavior:'smooth', block:'start'});
			});
		}
		if(mf){ mf.addEventListener('change',function(){
			if(!brands) return;
			brands.querySelectorAll('.bmb3-brand').forEach(function(x){ x.classList.toggle('is-active',(x.getAttribute('data-mf')||'')===mf.value); });
		}); }

		if(urlType){ apply(); setTimeout(function(){ grid.scrollIntoView({behavior:'smooth', block:'start'}); }, 200); }
	})();
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'bmb3_catalog', 'bmb3_catalog_shortcode' );
