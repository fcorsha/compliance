<?php
/**
 * BMB3 — דף רכב  v13 (גובה אחיד בין שתי הקוביות, גלריה פנורמית מלאה למעלה בקוביה הימנית)  shortcode: [bmb3_car]
 * דורש גם: [bmb3_finance] (snippet bmb3_finance) ו-[bmb3_lead_form] (snippet "BMB3 Lead Form") פעילים.
 * --------------------------------------------------------------------------
 * עדכון ב-WPCode: snippet "BMB3 Car Page" → מחק הכל → הדבק → Update.
 * דורש שה-snippet של [bmb3_finance] יהיה פעיל. הכפתורים מובילים לוואטסאפ של מושיקו.
 *
 * שדות ACF קיימים: manufacturer, car_model, year, mileage, price, sale_price,
 * hand, sku, status, test_date, image_1..image_8.
 * שדות מפרט נוספים (אופציונליים, יוצגו רק אם תיצור אותם ותמלא): fuel, gearbox,
 * drivetrain, engine_cc, seats, body_type.
 *
 * כל הפניות הקשר יוצאות לוואטסאפ של מושיקו: 972542159482.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'BMB3_WA_CONTACT' ) ) define( 'BMB3_WA_CONTACT', '972542159482' );

// Grant view_dealer_price to administrators once (no-op on subsequent loads)
add_action( 'init', function () {
	$role = get_role( 'administrator' );
	if ( $role && ! $role->has_cap( 'view_dealer_price' ) ) {
		$role->add_cap( 'view_dealer_price' );
	}
} );

if ( ! function_exists( 'bmb3_img_url' ) ) {
	function bmb3_img_url( $val ) {
		if ( empty( $val ) ) return '';
		if ( is_array( $val ) ) return isset( $val['url'] ) ? $val['url'] : '';
		if ( is_numeric( $val ) ) return wp_get_attachment_image_url( (int) $val, 'large' );
		return is_string( $val ) ? $val : '';
	}
}

function bmb3_car_page_shortcode() {
	$pid = get_the_ID();

	$manufacturer = trim( (string) get_field( 'manufacturer', $pid ) );
	$model        = trim( (string) get_field( 'car_model', $pid ) );
	$year         = trim( (string) get_field( 'year', $pid ) );
	$mileage      = get_field( 'mileage', $pid );
	$price        = get_field( 'price', $pid );        // מחירון
	$sale         = get_field( 'sale_price', $pid );   // מחיר מכירה לסוחר
	$hand         = trim( (string) get_field( 'hand', $pid ) );
	$sku          = trim( (string) get_field( 'sku', $pid ) );
	$status       = trim( (string) get_field( 'status', $pid ) );
	$test_date    = trim( (string) get_field( 'test_date', $pid ) );

	// שדות מפרט נוספים (אופציונליים)
	$fuel       = trim( (string) get_field( 'fuel', $pid ) );
	$gearbox    = trim( (string) get_field( 'gearbox', $pid ) );
	$drivetrain = trim( (string) get_field( 'drivetrain', $pid ) );
	$engine_cc  = trim( (string) get_field( 'engine_cc', $pid ) );
	$seats      = trim( (string) get_field( 'seats', $pid ) );
	$body_type  = trim( (string) get_field( 'body_type', $pid ) );
	$body_type  = trim( (string) get_field( 'body_type', $pid ) );
$body_type  = trim( (string) get_field( 'body_type', $pid ) );
$car_video_url = trim( (string) ( get_field( 'car_video_url', $pid ) ?? '' ) );

	$images = array();
	foreach ( array( 'image_1','image_2','image_3','image_4','image_5','image_6','image_7','image_8' ) as $f ) {
		$u = bmb3_img_url( get_field( $f, $pid ) );
		if ( $u ) $images[] = esc_url( $u );
	}

	$title       = trim( $manufacturer . ' ' . $model );
	if ( $title === '' ) $title = get_the_title();
	$price_fmt   = ( $price !== '' && $price !== null ) ? number_format( (float) $price ) : '';
	$sale_fmt    = ( $sale  !== '' && $sale  !== null ) ? number_format( (float) $sale )  : '';
	$mileage_fmt = ( $mileage !== '' && $mileage !== null ) ? number_format( (float) $mileage ) : '';
	$is_sold     = ( $status === 'sold' || $status === 'נמכר' );

	$share          = isset( $_GET['share'] );
	$logged         = is_user_logged_in();
	$can_see_dealer = current_user_can( 'view_dealer_price' );
	$share_url = add_query_arg( 'share', '1', urldecode( get_permalink( $pid ) ) );
$wa_share  = 'https://wa.me/?text=' . rawurlencode( $title . "\n\n" . $share_url );
	$wa_lines  = 'היי, מעוניין בפרטים על ' . $title . ' (מק"ט ' . $sku . ')';
	if ( $price_fmt )                          { $wa_lines .= "\n" . 'מחירון: ₪' . $price_fmt; }
	if ( $sale_fmt && ( $share || $can_see_dealer ) )  { $wa_lines .= "\n" . 'מחיר מכירה לסוחר: ₪' . $sale_fmt; }
	$wa_lead   = 'https://wa.me/' . BMB3_WA_CONTACT . '?text=' . rawurlencode( $wa_lines );

	// אייקונים
	$ic_cal   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M3 9h18M8 2.5v4M16 2.5v4"/></svg>';
	$ic_gauge = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 18a8 8 0 1 1 16 0"/><path d="M12 18l4-5"/><circle cx="12" cy="18" r="1.2" fill="currentColor"/></svg>';
	$ic_fuel  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="3" width="9" height="18" rx="1.5"/><path d="M4 10h9M16 7l3 3v7a2 2 0 0 1-4 0V5"/></svg>';
	$ic_gear  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="6" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><path d="M6 8v8M18 8v4a4 4 0 0 1-4 4H6"/></svg>';
	$ic_4x4   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="6" cy="17" r="2.5"/><circle cx="18" cy="17" r="2.5"/><path d="M4 12l2-4h7l3 4M8.5 17h7"/></svg>';
	$ic_eng   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 9h3l2-2h4v2h3l2 2v4h-2v3H8v-3l-3-1z"/><path d="M2 11v2M12 5V3"/></svg>';
	$ic_seat  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 4h8a2 2 0 0 1 2 2v8H8a2 2 0 0 1-2-2z"/><path d="M6 14l-2 6M16 14l2 6M5 20h14"/></svg>';
	$ic_body  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 13l2-5a3 3 0 0 1 2.8-2h8.4A3 3 0 0 1 19 8l2 5v4h-3M3 17v-4M6 17h12"/><circle cx="7.5" cy="17" r="1.5"/><circle cx="16.5" cy="17" r="1.5"/></svg>';
	$ic_user  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="3.4"/><path d="M5 20c0-3.5 3.1-6 7-6s7 2.5 7 6"/></svg>';
	$ic_test  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M3 9h18M8 2.5v4M16 2.5v4M9 14l2 2 4-4"/></svg>';

	// מפרט — רק שדות עם ערך
	$specs = array();
	if ( $year )        $specs[] = array( $ic_cal,   'שנתון',   esc_html( $year ) );
	if ( $mileage_fmt ) $specs[] = array( $ic_gauge, 'ק"מ',     esc_html( $mileage_fmt ) );
	if ( $fuel )        $specs[] = array( $ic_fuel,  'מנוע',   esc_html( $fuel ) );
	if ( $gearbox )     $specs[] = array( $ic_gear,  'תיבה',   esc_html( $gearbox ) );
	if ( $drivetrain )  $specs[] = array( $ic_4x4,   'הנעה',   esc_html( $drivetrain ) );
	if ( $engine_cc )   $specs[] = array( $ic_eng,   'נפח מנוע', esc_html( $engine_cc ) . ' סמ"ק' );
	if ( $seats )       $specs[] = array( $ic_seat,  'מקומות', esc_html( $seats ) );
	if ( $body_type )   $specs[] = array( $ic_body,  'מרכב',   esc_html( $body_type ) );
	if ( $test_date )   $specs[] = array( $ic_test, 'טסט',     esc_html( $test_date ) );
	if ( $hand )        $specs[] = array( $ic_user,  'יד',     esc_html( $hand ) );

	// רכבים דומים
	$similar_html = '';
	if ( ! $is_sold ) {
		$sim_args = array( 'post_type'=>'car','post_status'=>'publish','posts_per_page'=>5,
			'post__not_in'=>array($pid),'orderby'=>'date','order'=>'DESC' );
		if ( $manufacturer !== '' ) $sim_args['meta_query'] = array( array('key'=>'manufacturer','value'=>$manufacturer,'compare'=>'=') );
		$sim = new WP_Query( $sim_args );
		if ( ! $sim->have_posts() && $manufacturer !== '' ) { wp_reset_postdata();
			$sim = new WP_Query( array('post_type'=>'car','post_status'=>'publish','posts_per_page'=>5,'post__not_in'=>array($pid),'orderby'=>'date','order'=>'DESC') ); }
		$sc=''; $sn=0;
		if ( $sim->have_posts() ) { while ( $sim->have_posts() ) { $sim->the_post();
			if ( $sn >= 4 ) break;
			$spid=get_the_ID(); $sstatus=trim((string)get_field('status',$spid));
			if ( $sstatus==='sold'||$sstatus==='נמכר' ) continue;
			$sname=trim(get_field('manufacturer',$spid).' '.get_field('car_model',$spid)); if($sname==='')$sname=get_the_title();
			$syear=trim((string)get_field('year',$spid)); $smile=get_field('mileage',$spid); $sprice=get_field('price',$spid);
			$simg=esc_url(bmb3_img_url(get_field('image_1',$spid)));
			$smeta=array(); if($syear)$smeta[]=esc_html($syear); if($smile!==''&&$smile!==null)$smeta[]=number_format((float)$smile).' ק"מ';
			$spf=($sprice!==''&&$sprice!==null)?number_format((float)$sprice):'';
			ob_start(); ?>
			<a class="bmb3cp-sim-card" href="<?php echo esc_url(get_permalink()); ?>">
				<div class="bmb3cp-sim-img"><?php if($simg):?><img src="<?php echo $simg;?>" alt="<?php echo esc_attr($sname);?>" loading="lazy"><?php endif;?></div>
				<div class="bmb3cp-sim-b"><h4><?php echo esc_html($sname);?></h4>
					<?php if($smeta):?><div class="bmb3cp-sim-meta"><?php echo implode(' · ',$smeta);?></div><?php endif;?>
					<?php if($spf):?><div class="bmb3cp-sim-price"><span>מחירון</span> <?php echo esc_html($spf);?> ₪</div><?php endif;?>
				</div>
			</a>
			<?php $sc.=ob_get_clean(); $sn++;
		} }
		wp_reset_postdata();
		if ( $sn>0 ) $similar_html='<div class="bmb3cp-sim"><div class="bmb3cp-sim-head"><h2>רכבים דומים שיכולים לעניין אותך</h2><a href="/catalog/">לכל הקטלוג ←</a></div><div class="bmb3cp-sim-grid">'.$sc.'</div></div>';
	}

	// אייקון וואטסאפ (בשימוש כפול: הודעה לסוכן + שתף)
	$wa_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.15-1.7-.83-1.96-.93-.26-.1-.45-.15-.64.15-.19.29-.74.93-.9 1.12-.17.19-.33.21-.62.07-.29-.15-1.23-.45-2.34-1.44-.86-.77-1.45-1.72-1.62-2.01-.17-.29-.02-.45.13-.59.13-.13.29-.34.43-.51.15-.17.19-.29.29-.48.1-.19.05-.36-.02-.51-.07-.15-.64-1.55-.88-2.12-.23-.55-.47-.48-.64-.49l-.55-.01c-.19 0-.5.07-.76.36-.26.29-1 .98-1 2.38s1.02 2.76 1.17 2.95c.15.19 2.01 3.07 4.88 4.3.68.29 1.21.47 1.62.6.68.22 1.31.19 1.8.12.55-.08 1.7-.69 1.94-1.36.24-.67.24-1.24.17-1.36-.07-.12-.26-.19-.55-.34zM12 2a10 10 0 0 0-8.5 15.27L2 22l4.85-1.27A10 10 0 1 0 12 2z"/></svg>';

	ob_start(); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;500;600;700;800&display=swap">

	<div class="bmb3cp<?php echo $is_sold?' is-sold':''; ?>" dir="rtl">

		<?php if ( $can_see_dealer && ! $share && $sale_fmt && ! $is_sold ) : ?>
		<button id="bmb3cp-eye" type="button" aria-pressed="false" aria-label="הצג או הסתר מחיר סוחר">
			<svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
			<svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.5 10.5 0 0 1 12 19c-6.5 0-10-7-10-7a18.2 18.2 0 0 1 5.06-5.94M9.9 4.24A9.6 9.6 0 0 1 12 4c6.5 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19M1 1l22 22"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
		</button>
		<?php endif; ?>

		<div class="bmb3cp-inner">

		<div class="bmb3cp-grid<?php echo $is_sold?' no-calc':''; ?>">

			<div class="bmb3cp-card bmb3cp-leftcard">
				<div class="bmb3cp-leftgrid">

					<div class="bmb3cp-gallery">
						<span class="bmb3cp-badge <?php echo $is_sold?'sold':'avail'; ?>"><?php echo $is_sold?'נמכר':'מוכן למימון'; ?></span>
						<?php if ( $car_video_url ) : ?>
    <div class="bmb3cp-photo bmb3cp-has-video">
        <video class="bmb3cp-video" autoplay muted loop playsinline poster="<?php echo isset( $images[0] ) ? $images[0] : ''; ?>">
            <source src="<?php echo esc_url( $car_video_url ); ?>" type="video/mp4">
        </video>
    </div>
<?php elseif ( $images ) : ?>
    <div class="bmb3cp-photo"><img class="bmb3cp-main" src="<?php echo $images[0]; ?>" alt="<?php echo esc_attr($title); ?>"></div>
							<?php if ( count($images)>1 ) : ?>
							<div class="bmb3cp-thumbs">
								<?php $shown = array_slice($images,0,4); foreach ( $shown as $i=>$img ) : $extra = ($i===3 && count($images)>4); ?>
									<button type="button" class="bmb3cp-thumb<?php echo $i===0?' active':''; ?><?php echo $extra?' more':''; ?>" data-src="<?php echo $img; ?>" data-i="<?php echo $i; ?>">
										<img src="<?php echo $img; ?>" alt="">
										<?php if ( $extra ) : ?><span class="more-lbl">+<?php echo count($images)-3; ?></span><?php endif; ?>
									</button>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
						<?php else : ?>
							<div class="bmb3cp-photo empty"><?php echo $ic_body; ?><span>התמונות יתווספו בקרוב</span></div>
						<?php endif; ?>
					</div>

					<div class="bmb3cp-info">
						<h1 class="bmb3cp-title"><?php echo esc_html($title); ?></h1>
						<?php if ( $year ) : ?><div class="bmb3cp-year"><?php echo esc_html($year); ?></div><?php endif; ?>

						<?php if ( $price_fmt ) : ?>
						<div class="bmb3cp-price"><span class="lbl">מחיר מחירון</span><span class="cur">₪</span> <?php echo esc_html($price_fmt); ?></div>
						<?php endif; ?>

						<?php if ( $sale_fmt && ! $is_sold && ( $share || $can_see_dealer ) ) : ?>
						<div class="bmb3cp-sale<?php echo $share?'':' bmb3cp-dealer'; ?>"><span>מחיר מכירה לסוחר</span><b><?php echo esc_html($sale_fmt); ?> ₪</b></div>
						<div class="bmb3cp-diff<?php echo $share?'':' bmb3cp-dealer'; ?>" data-sale="<?php echo esc_attr( (float) $sale ); ?>" data-price="<?php echo esc_attr( (float) $price ); ?>">
							<span class="bmb3cp-diff-emoji">🤑</span>
							<div class="bmb3cp-diff-txt">
								<span>רווח לסוחר</span>
								<b class="bmb3cp-diff-v">—</b>
							</div>
						</div>
						<?php endif; ?>

						<?php if ( $specs ) : ?>
						<div class="bmb3cp-specs">
							<?php foreach ( $specs as $s ) : ?>
							<div class="sp"><span class="ic"><?php echo $s[0]; ?></span><span class="k"><?php echo $s[1]; ?></span><span class="v"><?php echo $s[2]; ?></span></div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>

						<ul class="bmb3cp-checks">
							<li>בדיקת מכון רישוי זמינה</li>
							<li>דוח עבר רכב נקי</li>
							<li>רכב לאחר השבחה ובדיקה</li>
							<li>תהליך מסירה מסודר ואמין</li>
						</ul>

						<?php if ( ! $is_sold ) : ?>
						<div class="bmb3cp-cta-row">
							<a class="bmb3cp-cta primary" href="tel:+<?php echo esc_attr( BMB3_WA_CONTACT ); ?>">
								<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.5.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.5.1.4 0 .8-.2 1l-2.3 2.3z"/></svg>
								התקשר לסוכן
							</a>
							<a class="bmb3cp-cta wa" href="<?php echo esc_url($wa_lead); ?>" target="_blank" rel="noopener">
								<?php echo $wa_svg; ?>
								הודעה לסוכן
							</a>
						</div>
						<a class="bmb3cp-cta bmb3cp-share" href="<?php echo esc_url($wa_share); ?>" target="_blank" rel="noopener">
							<?php echo $wa_svg; ?>
							שתף את הרכב בוואטסאפ
						</a>
						<?php else : ?>
						<div class="bmb3cp-sold-note">הרכב נמכר. דברו איתנו ונמצא לכם רכב דומה.</div>
						<?php endif; ?>
					</div>

				</div>
			</div>

			<?php if ( ! $is_sold ) : ?>
			<div class="bmb3cp-calc"><?php echo do_shortcode('[bmb3_finance]'); ?></div>
			<?php endif; ?>

		</div>

		<div class="bmb3cp-feats">
			<div class="ft"><span class="fi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2.5l7 2.8v6c0 4.6-3 8.3-7 10-4-1.7-7-5.4-7-10v-6z"/><path d="M9 12l2 2 4-4"/></svg></span><b>אישור מהיר</b><i>תשובה תוך דקות</i></div>
			<div class="ft"><span class="fi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-5h6v5"/></svg></span><b>מגוון חברות מימון</b><i>תנאים מותאמים</i></div>
			<div class="ft"><span class="fi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M8 14l8-8M9 9h.01M15 15h.01"/></svg></span><b>מימון עד 100%</b><i>בהתאם לזכאות</i></div>
			<div class="ft"><span class="fi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M3 9h18M8 2.5v4M16 2.5v4"/></svg></span><b>פריסה גמישה</b><i>עד 100 תשלומים</i></div>
			<div class="ft"><span class="fi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 2.7-5 6-5s6 2 6 5"/><path d="M16 6.5a3 3 0 0 1 0 5"/></svg></span><b>ליווי אישי</b><i>צוות זמין לכל שאלה</i></div>
		</div>

		<?php echo $similar_html; ?>

		</div>

		<?php if ( $images ) : ?>
		<div class="bmb3cp-lb" data-images='<?php echo esc_attr( wp_json_encode($images) ); ?>' aria-hidden="true">
			<button class="bmb3cp-lb-close" type="button" aria-label="סגור">&times;</button>
			<button class="bmb3cp-lb-nav prev" type="button" aria-label="הקודם">&#8250;</button>
			<div class="bmb3cp-lb-stage"><img class="bmb3cp-lb-img" src="" alt="<?php echo esc_attr($title); ?>"></div>
			<button class="bmb3cp-lb-nav next" type="button" aria-label="הבא">&#8249;</button>
			<div class="bmb3cp-lb-count"><span class="cur">1</span> / <span class="total"><?php echo count($images); ?></span></div>
		</div>
		<?php endif; ?>

		<?php if ( ! $is_sold ) : ?>
		<div class="bmb3cp-modal" id="bmb3-lead-modal" aria-hidden="true">
			<div class="bmb3cp-modal-overlay" data-bmb3-close></div>
			<div class="bmb3cp-modal-box" role="dialog" aria-modal="true" aria-labelledby="bmb3-lead-title">
				<button type="button" class="bmb3cp-modal-close" data-bmb3-close aria-label="סגור">&times;</button>
				<h3 id="bmb3-lead-title" class="bmb3cp-modal-title">השאירו פרטים ונחזור אליכם</h3>
				<p class="bmb3cp-modal-sub"><?php echo esc_html($title); ?><?php echo $price_fmt ? ' · ₪'.$price_fmt : ''; ?></p>
				<?php echo do_shortcode('[bmb3_lead_form]'); ?>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<style>
	.bmb3cp{
		--bg:#0E0F13; --card:#FFFFFF; --ink:#15202B; --muted:#5C6B7A; --line:#E7ECF1;
		--green:#5FBE3C; --green-d:#46992A; --green-l:#86D766; --dark:#0E0F13;
		font-family:'Assistant',sans-serif; color:var(--ink); direction:rtl; box-sizing:border-box;
		background:var(--bg); overflow:hidden;
		width:100vw; position:relative; left:50%; right:50%; margin-left:-50vw; margin-right:-50vw;

		/* שתי השורות החדשות שמעיפות את הפסים הלבנים: */
		margin-top: -100px !important;
		margin-bottom: -130px !important;
	}
	}
	.bmb3cp *{box-sizing:border-box; font-family:'Assistant',sans-serif;}
	.bmb3cp [hidden]{display:none !important;}

	/* התוכן עצמו נשאר ברוחב פרימיום ממורכז, כדי שלא יידבק לקצוות המסך */
	.bmb3cp-inner{max-width:1300px; width:100%; margin-inline:auto; padding:clamp(16px,2.5vw,32px) 16px;}

	.bmb3cp-card{background:var(--card); border-radius:20px; padding:clamp(16px,2.5vw,28px); box-shadow:0 30px 60px -30px rgba(0,0,0,.6); height:100%;}

	/* שני כרטיסים: מפרט+גלריה מימין, מחשבון משמאל — גובה אחיד */
	.bmb3cp-grid{display:grid; grid-template-columns:1fr 1.06fr; gap:24px; align-items:stretch;}
	.bmb3cp-grid.no-calc{grid-template-columns:1fr;}
	.bmb3cp-calc{height:100%;}
	/* קוביה ימנית: גלריה מלאה למעלה, מפרט מתחת */
	.bmb3cp-leftgrid{display:flex; flex-direction:column; gap:24px;}

	/* גלריה */
	.bmb3cp-gallery{position:relative;}
	.bmb3cp-badge{position:absolute; top:12px; inset-inline-start:12px; z-index:3; font-size:11px; font-weight:800; letter-spacing:.06em; padding:6px 13px; border-radius:8px; color:#0c1408;}
	.bmb3cp-badge.avail{background:linear-gradient(135deg,var(--green-l),var(--green-d)); color:#0c1408;}
	.bmb3cp-badge.sold{background:#9aa3b0; color:#fff;}
	.bmb3cp-photo{position:relative; border-radius:14px; overflow:hidden; aspect-ratio:16/9; background:#0c1426; cursor:zoom-in;}
	.bmb3cp-photo img{width:100%; height:100%; object-fit:cover; display:block; transition:transform .8s cubic-bezier(.2,.8,.2,1);}
	.bmb3cp-photo:hover img{transform:scale(1.05);}
	.bmb3cp-photo.empty{display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; color:#9fb0c8; cursor:default;}
	.bmb3cp-photo.empty svg{width:54px; height:54px;}
	.bmb3cp-thumbs{display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-top:10px;}
	.bmb3cp-thumb{position:relative; aspect-ratio:1/1; border-radius:9px; overflow:hidden; padding:0; cursor:pointer; border:1.5px solid var(--line); background:#fff; opacity:.85; transition:all .2s;}
	.bmb3cp-thumb img{width:100%; height:100%; object-fit:cover;}
	.bmb3cp-thumb:hover{opacity:1;}
	.bmb3cp-thumb.active{opacity:1; border-color:var(--green); box-shadow:0 0 0 1px var(--green);}
	.bmb3cp-thumb.more .more-lbl{position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(14,15,19,.62); color:#fff; font-size:17px; font-weight:800;}

	/* מפרט */
	.bmb3cp-title{font-size:clamp(20px,2.1vw,26px); font-weight:800; color:var(--ink); margin:0; line-height:1.15;}
	.bmb3cp-year{color:var(--muted); font-size:14px; font-weight:600; margin-top:3px;}
	.bmb3cp-price{margin:14px 0 4px; font-size:clamp(20px,2.2vw,24px); font-weight:800; color:var(--green-d); line-height:1;}
	.bmb3cp-price .lbl{display:block; font-size:12px; font-weight:600; color:var(--muted);}
	.bmb3cp-price .cur{font-size:.7em;}
	.bmb3cp-sale{display:flex; align-items:center; justify-content:space-between; gap:10px; margin:10px 0; padding:13px 16px; border-radius:13px; background:rgba(37,211,102,.12); border:1.5px solid #25D366; box-shadow:0 8px 22px -12px rgba(37,211,102,.5); font-size:14px;}
	.bmb3cp-sale span{color:#1a7a3c; font-weight:700;} .bmb3cp-sale b{color:#128a3e; font-size:19px;}
	.bmb3cp-dealer{display:none !important;}
	body.bmb3cp-show-price .bmb3cp-dealer{display:flex !important;}
	/* שני כפתורי CTA — גובה קבוע, לא נמתחים */
	.bmb3cp-cta-row{display:flex !important; align-items:center !important; gap:10px; margin-top:18px;}
	.bmb3cp-cta-row .bmb3cp-cta{flex:1 1 0 !important; height:56px !important; min-height:0 !important; max-height:56px !important; padding:0 16px !important; font-size:16px; align-self:center !important; overflow:hidden;}
	.bmb3cp-cta-row .bmb3cp-cta svg{width:20px !important; height:20px !important; flex:0 0 auto !important;}
	.bmb3cp-cta-row .bmb3cp-cta.wa{background:#25D366; color:#0c1408;}
	.bmb3cp-cta-row .bmb3cp-cta.wa:hover{background:#1ebe5d;}
	/* שדה הפרש מזומן לסוחר */
	.bmb3cp-diff{display:flex; align-items:center; gap:13px; margin:10px 0; padding:14px 16px; border-radius:14px;
		background:linear-gradient(135deg, rgba(37,211,102,.16), rgba(37,211,102,.06)); border:1.5px solid #25D366; box-shadow:0 8px 22px -12px rgba(37,211,102,.5);}
	.bmb3cp-diff-emoji{font-size:32px; line-height:1; filter:drop-shadow(0 2px 5px rgba(0,0,0,.18));}
	.bmb3cp-diff-txt{display:flex; flex-direction:column; gap:2px;}
	.bmb3cp-diff-txt span{font-size:12.5px; color:var(--muted); font-weight:600;}
	.bmb3cp-diff-txt b{font-size:23px; font-weight:800; color:var(--green-d); line-height:1.1;}

	.bmb3cp-specs{display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin:18px 0;}
	.bmb3cp-specs .sp{display:flex; flex-direction:column; align-items:center; text-align:center; gap:7px; padding:18px 8px; border:1px solid var(--line); border-radius:14px;}
	.bmb3cp-specs .ic{width:34px; height:34px; color:var(--green-d);} .bmb3cp-specs .ic svg{width:100%; height:100%;}
	.bmb3cp-specs .k{font-size:12px; color:var(--muted);}
	.bmb3cp-specs .v{font-size:19px; font-weight:800; color:var(--ink);}

	.bmb3cp-checks{list-style:none; margin:16px 0; padding:0; display:flex; flex-direction:column; gap:9px;}
	.bmb3cp-checks li{position:relative; padding-inline-start:26px; font-size:14px; color:#33414f;}
	.bmb3cp-checks li::before{content:""; position:absolute; inset-inline-start:0; top:1px; width:18px; height:18px; border-radius:50%;
		background:var(--green) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12l5 5 9-10'/%3E%3C/svg%3E") center/12px no-repeat;}

	.bmb3cp-cta-row{display:flex; gap:10px; margin-top:18px;}
	.bmb3cp-cta{flex:1; display:flex; align-items:center; justify-content:center; gap:8px; border:0; cursor:pointer; border-radius:12px; padding:14px; font-family:inherit; font-size:15.5px; font-weight:800; text-decoration:none; transition:transform .2s, box-shadow .2s;}
	.bmb3cp-cta.primary{background:linear-gradient(135deg,var(--green-l),var(--green-d)); color:#0c1408; box-shadow:0 12px 26px -12px rgba(95,190,60,.6);}
	.bmb3cp-cta.primary:hover{transform:translateY(-2px); box-shadow:0 18px 34px -12px rgba(95,190,60,.7);}
	.bmb3cp-cta.wa{flex:0 0 auto; padding:14px 18px; background:var(--dark); color:#fff;}
	.bmb3cp-cta.wa:hover{transform:translateY(-2px);}
	.bmb3cp-cta.wa svg{width:19px; height:19px;}
	.bmb3cp-formwrap{margin-top:14px; animation:bmb3f .3s ease;}
	@keyframes bmb3f{from{opacity:0; transform:translateY(-5px);} to{opacity:1; transform:none;}}

	/* כפתור שתף — עיצוב זהה לכפתורי CTA, רוחב מלא, ירוק וואטסאפ */
	.bmb3cp-share{
		display:flex; align-items:center; justify-content:center; gap:8px;
		width:100%; margin-top:10px; border-radius:12px; padding:14px 16px;
		font-family:inherit; font-size:15.5px; font-weight:800;
		text-decoration:none; color:#0c1408;
		background:#25D366;
		box-shadow:0 12px 26px -12px rgba(37,211,102,.5);
		transition:transform .2s, box-shadow .2s;
	}
	.bmb3cp-share:hover{transform:translateY(-2px); box-shadow:0 18px 34px -12px rgba(37,211,102,.65); text-decoration:none; color:#0c1408;}
	.bmb3cp-share svg{width:20px; height:20px; flex:0 0 auto;}

	.bmb3cp-sold-note{margin-top:14px; text-align:center; color:var(--muted); padding:14px; border:1px dashed var(--line); border-radius:12px;}

	/* מחשבון - מתיחה מלאה פנימית ופיזור טקסט מאוזן */
	.bmb3cp-calc .bmb3fin{
		margin:0;
		height:100%;
		display:flex !important;
		flex-direction:column !important;
		justify-content:space-between !important;
		gap:24px !important;
	}

	/* פס יתרונות */
	.bmb3cp-feats{display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-top:18px; padding:22px clamp(14px,2vw,26px); background:var(--dark); border-radius:18px;}
	.bmb3cp-feats .ft{display:flex; flex-direction:column; align-items:center; text-align:center; gap:4px; color:#EAF0EA;}
	.bmb3cp-feats .fi{width:34px; height:34px; color:var(--green-l);} .bmb3cp-feats .fi svg{width:100%; height:100%;}
	.bmb3cp-feats b{font-size:14.5px; font-weight:800; color:#fff;}
	.bmb3cp-feats i{font-size:12px; font-style:normal; color:#9aa3b0;}

	/* רכבים דומים */
	.bmb3cp-sim{margin-top:24px;}
	.bmb3cp-sim-head{display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin-bottom:14px; flex-wrap:wrap;}
	.bmb3cp-sim-head h2{font-size:clamp(18px,2vw,24px); font-weight:800; color:#fff; margin:0;}
	.bmb3cp-sim-head a{color:var(--green-l); font-weight:700; font-size:14px; text-decoration:none;}
	.bmb3cp-sim-grid{display:grid; grid-template-columns:repeat(4,1fr); gap:14px;}
	.bmb3cp-sim-card{display:flex; flex-direction:column; text-decoration:none; background:var(--card); border-radius:13px; overflow:hidden; transition:transform .3s, box-shadow .3s;}
	.bmb3cp-sim-card:hover{transform:translateY(-5px); box-shadow:0 24px 44px -28px rgba(0,0,0,.6);}
	.bmb3cp-sim-img{aspect-ratio:16/11; background:#E7EAF1; overflow:hidden;}
	.bmb3cp-sim-img img{width:100%; height:100%; object-fit:cover; transition:transform .6s;}
	.bmb3cp-sim-card:hover .bmb3cp-sim-img img{transform:scale(1.06);}
	.bmb3cp-sim-b{padding:12px 13px 14px;} .bmb3cp-sim-b h4{margin:0; font-size:15px; font-weight:800; color:var(--ink);}
	.bmb3cp-sim-meta{font-size:12px; color:var(--muted); margin-top:3px;}
	.bmb3cp-sim-price{margin-top:5px; font-size:14.5px; font-weight:800; color:var(--green-d);}
	.bmb3cp-sim-price span{font-weight:500; font-size:11px; color:var(--muted);}

	/* כפתור עין */
	#bmb3cp-eye{position:fixed; inset-inline-start:16px; top:16px; z-index:9990; width:50px; height:50px; padding:0; border:0; border-radius:999px; cursor:pointer;
		background:var(--dark); color:var(--green-l); box-shadow:0 12px 28px -10px rgba(0,0,0,.6); transition:transform .2s, background .2s, color .2s;}
	#bmb3cp-eye:hover{transform:translateY(-2px);}
	#bmb3cp-eye svg{width:21px; height:21px;}
	#bmb3cp-eye .eye-on{display:none;} #bmb3cp-eye .eye-off{display:block;}
	#bmb3cp-eye.on{background:linear-gradient(135deg,var(--green-l),var(--green-d)); color:#0c1408;}
	#bmb3cp-eye.on .eye-on{display:block;} #bmb3cp-eye.on .eye-off{display:none;}

	/* לייטבוקס */
	.bmb3cp-lb{position:fixed; inset:0; z-index:10000; display:none; align-items:center; justify-content:center; background:rgba(8,10,16,.95); padding:clamp(12px,4vw,48px);}
	.bmb3cp-lb.open{display:flex;}
	.bmb3cp-lb-img{max-width:100%; max-height:88vh; border-radius:12px; object-fit:contain;}
	.bmb3cp-lb-close{position:absolute; top:18px; inset-inline-end:20px; width:44px; height:44px; border-radius:999px; border:0; cursor:pointer; background:rgba(255,255,255,.1); color:#fff; font-size:26px;}
	.bmb3cp-lb-nav{position:absolute; top:50%; transform:translateY(-50%); width:52px; height:52px; border-radius:999px; border:1px solid rgba(134,215,102,.4); cursor:pointer; background:rgba(255,255,255,.05); color:var(--green-l); font-size:28px;}
	.bmb3cp-lb-nav.prev{inset-inline-end:18px;} .bmb3cp-lb-nav.next{inset-inline-start:18px;}
	.bmb3cp-lb-count{position:absolute; bottom:22px; inset-inline-start:50%; transform:translateX(-50%); color:var(--green-l); font-weight:700; background:rgba(0,0,0,.3); padding:6px 14px; border-radius:999px;}
	.bmb3cp-lb.single .bmb3cp-lb-nav,.bmb3cp-lb.single .bmb3cp-lb-count{display:none;}

	/* פופ-אפ ליד */
	.bmb3cp-modal{--card:#FFFFFF; --ink:#15202B; --muted:#5C6B7A; --line:#E7ECF1; --green:#5FBE3C; --green-d:#46992A; --green-l:#86D766; position:fixed; inset:0; z-index:10001; display:none; align-items:center; justify-content:center; padding:clamp(12px,4vw,40px); font-family:'Assistant',sans-serif; direction:rtl;}
	.bmb3cp-modal.open{display:flex;}
	.bmb3cp-modal-overlay{position:absolute; inset:0; background:rgba(8,10,16,.72); backdrop-filter:blur(2px);}
	.bmb3cp-modal-box{position:relative; z-index:1; width:100%; max-width:430px; max-height:90vh; overflow:auto;
		background:var(--card); color:var(--ink); border-radius:20px; padding:clamp(22px,3vw,32px);
		box-shadow:0 40px 80px -28px rgba(0,0,0,.7); direction:rtl; text-align:right;}
	.bmb3cp-modal-close{position:absolute; top:14px; inset-inline-end:14px; width:36px; height:36px; border:0; border-radius:999px; cursor:pointer;
		background:#F5F8FA; color:var(--ink); font-size:20px; line-height:1; transition:background .2s;}
	.bmb3cp-modal-close:hover{background:var(--line);}
	.bmb3cp-modal-title{margin:0 0 4px; font-size:19px; font-weight:800; color:var(--ink);}
	.bmb3cp-modal-sub{margin:0 0 18px; font-size:13px; color:var(--muted);}

	/* טופס הליד */
	.bmb3cp-modal-box .bmb3-lead{display:flex; flex-direction:column; gap:13px;}
	.bmb3cp-modal-box .bmb3-lead label{display:flex; flex-direction:column; gap:6px; font-size:13px; font-weight:600; color:var(--muted);}
	.bmb3cp-modal-box .bmb3-lead input{font-family:inherit; font-size:15px; padding:13px 14px; border:1px solid var(--line); border-radius:11px; background:#fff; color:var(--ink); outline:0; transition:border-color .2s, box-shadow .2s;}
	.bmb3cp-modal-box .bmb3-lead input:focus{border-color:var(--green); box-shadow:0 0 0 3px rgba(95,190,60,.15);}
	.bmb3cp-modal-box .bmb3-lead button[type=submit]{margin-top:4px; border:0; cursor:pointer; border-radius:13px; padding:15px;
		font-family:inherit; font-size:16px; font-weight:800; color:#0c1408;
		background:linear-gradient(135deg,var(--green-l),var(--green-d)); box-shadow:0 12px 26px -12px rgba(95,190,60,.6); transition:transform .2s, box-shadow .2s;}
	.bmb3cp-modal-box .bmb3-lead button[type=submit]:hover{transform:translateY(-2px); box-shadow:0 18px 34px -12px rgba(95,190,60,.7);}
	.bmb3cp-modal-box .bmb3-lead button[disabled]{opacity:.6; cursor:default; transform:none; box-shadow:none;}
	.bmb3cp-modal-box .bmb3-lead-msg{margin:8px 0 0; font-size:13px; color:var(--muted); text-align:center;}

	@media(max-width:1024px){
		.bmb3cp-grid{grid-template-columns:1fr;}
	}
	@media(max-width:720px){
		.bmb3cp-specs{grid-template-columns:repeat(2,1fr);}
		.bmb3cp-feats{grid-template-columns:repeat(2,1fr);}
		.bmb3cp-sim-grid{grid-template-columns:1fr 1fr;}
	}
	@media(prefers-reduced-motion:reduce){.bmb3cp *{animation:none!important; transition:none!important;}}
	</style>

	<script>
	function bmb3Reveal(btn){
		var info=btn.closest('.bmb3cp-info'); if(!info) return;
		var wrap=info.querySelector('.bmb3cp-formwrap'); if(wrap) wrap.removeAttribute('hidden');
		btn.textContent='השאירו פרטים ונחזור אליכם';
	}
	document.addEventListener('click',function(e){
		var t=e.target.closest('.bmb3cp-thumb'); if(!t) return;
		var g=t.closest('.bmb3cp-gallery'), main=g.querySelector('.bmb3cp-main');
		if(t.classList.contains('more')){ var lb=document.querySelector('.bmb3cp-lb'); if(lb){ lb.dispatchEvent(new CustomEvent('bmb3open',{detail:+t.getAttribute('data-i')})); return; } }
		if(main){ main.style.opacity='0'; setTimeout(function(){ main.src=t.getAttribute('data-src'); main.style.opacity='1'; },110); }
		g.querySelectorAll('.bmb3cp-thumb').forEach(function(x){x.classList.remove('active');}); t.classList.add('active');
	});
	(function(){
		var KEY='bmb3_show_dealer', btn=document.getElementById('bmb3cp-eye'); if(!btn) return;
		function apply(on){ document.body.classList.toggle('bmb3cp-show-price',on); btn.classList.toggle('on',on); btn.setAttribute('aria-pressed',on?'true':'false'); }
		var saved=false; try{ saved=(localStorage.getItem(KEY)==='1'); }catch(e){}
		apply(saved);
		btn.addEventListener('click',function(){ var on=!btn.classList.contains('on'); try{ localStorage.setItem(KEY,on?'1':'0'); }catch(e){} apply(on); });
	})();
		(function(){
		var box=document.querySelector('.bmb3cp-diff'); if(!box) return;
		var sale=parseFloat(box.getAttribute('data-sale'))||0;
		var price=parseFloat(box.getAttribute('data-price'))||0;
		var out=box.querySelector('.bmb3cp-diff-v');
		var ILS=function(n){return new Intl.NumberFormat('he-IL',{style:'currency',currency:'ILS',maximumFractionDigits:0}).format(Math.round(n||0));};
		function sellPrice(){
			var el=document.querySelector('.bmb3cp-calc .fin-price');
			if(el){ var v=parseInt(String(el.value).replace(/[^\d]/g,''),10); if(!isNaN(v) && v>0) return v; }
			return price;
		}
		function render(){
			var d=sellPrice()-sale;
			out.textContent=(d>=0?'+':'')+ILS(d);
			out.style.color = d>=0 ? 'var(--green-d)' : '#b32d2e';
		}
		var priceEl=document.querySelector('.bmb3cp-calc .fin-price');
		if(priceEl){ priceEl.addEventListener('input',render); priceEl.addEventListener('blur',render); }
		render();
	})();
	(function(){
		var lb=document.querySelector('.bmb3cp-lb'); if(!lb) return;
		var imgs=[]; try{ imgs=JSON.parse(lb.getAttribute('data-images')||'[]'); }catch(e){}
		if(!imgs.length) return; if(imgs.length<2) lb.classList.add('single');
		try{ document.body.appendChild(lb); }catch(e){}
		var big=lb.querySelector('.bmb3cp-lb-img'), curEl=lb.querySelector('.cur'), idx=0;
		function show(i){ idx=(i+imgs.length)%imgs.length; big.src=imgs[idx]; if(curEl) curEl.textContent=idx+1; }
		function open(i){ show(i||0); lb.classList.add('open'); lb.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
		function close(){ lb.classList.remove('open'); lb.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }
		var photo=document.querySelector('.bmb3cp-photo:not(.empty)'), main=document.querySelector('.bmb3cp-main');
		if(photo){ photo.addEventListener('click',function(){ var s=main?main.getAttribute('src'):''; var f=imgs.indexOf(s); open(f>=0?f:0); }); }
		lb.addEventListener('bmb3open',function(e){ open(e.detail||0); });
		lb.querySelector('.bmb3cp-lb-close').addEventListener('click',close);
		var pv=lb.querySelector('.prev'), nx=lb.querySelector('.next');
		if(pv) pv.addEventListener('click',function(e){ e.stopPropagation(); show(idx-1); });
		if(nx) nx.addEventListener('click',function(e){ e.stopPropagation(); show(idx+1); });
		lb.addEventListener('click',function(e){ if(e.target===lb) close(); });
		document.addEventListener('keydown',function(e){ if(!lb.classList.contains('open')) return; if(e.key==='Escape') close(); else if(e.key==='ArrowLeft') show(idx+1); else if(e.key==='ArrowRight') show(idx-1); });
		var sx=0; lb.addEventListener('touchstart',function(e){ sx=e.changedTouches[0].clientX; },{passive:true});
		lb.addEventListener('touchend',function(e){ var dx=e.changedTouches[0].clientX-sx; if(Math.abs(dx)>40) show(dx<0?idx+1:idx-1); },{passive:true});
	})();
	(function(){
    var modal=document.getElementById('bmb3-lead-modal'); if(!modal) return;
    try{ document.body.appendChild(modal); }catch(e){}
    var savedScroll=0;
    function openM(){ savedScroll=window.scrollY||document.documentElement.scrollTop; modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
    function closeM(){ modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); document.body.style.overflow=''; window.scrollTo(0, savedScroll); }
		document.querySelectorAll('[data-bmb3-open="bmb3-lead-modal"]').forEach(function(b){ b.addEventListener('click', openM); });
		modal.querySelectorAll('[data-bmb3-close]').forEach(function(b){ b.addEventListener('click', closeM); });
		document.addEventListener('keydown',function(e){ if(e.key==='Escape' && modal.classList.contains('open')) closeM(); });
	})();
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'bmb3_car', 'bmb3_car_page_shortcode' );
