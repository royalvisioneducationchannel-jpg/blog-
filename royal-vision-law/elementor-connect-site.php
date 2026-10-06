<?php
/**
 * Royal Vision Law: articles, Insights page, legal pages, shared header/footer.
 * Run after elementor-build.php and elementor-update-content.php (Novamira execute-php).
 * Idempotent: everything is matched by the _rvl_key meta and updated in place.
 */

$home_id = 22;
$home    = home_url( '/' );
$phone   = '+92 334 5298703';
$tel     = 'tel:+923345298703';
$today   = date_i18n( 'j F Y' );
$rv_id   = function () { return substr( md5( uniqid( '', true ) . mt_rand() ), 0, 7 ); };
$px      = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'sizes' => array() ); };
$pad     = function ( $t, $r, $b, $l ) { return array( 'unit' => 'px', 'top' => (string) $t, 'right' => (string) $r, 'bottom' => (string) $b, 'left' => (string) $l, 'isLinked' => false ); };
$gap     = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'column' => (string) $n, 'row' => (string) $n, 'isLinked' => true ); };
$link    = function ( $url ) { return array( 'url' => $url, 'is_external' => '', 'nofollow' => '', 'custom_attributes' => '' ); };
$con     = function ( $s, $els = array(), $inner = true ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'container', 'isInner' => $inner, 'settings' => $s, 'elements' => $els ); };
$wid     = function ( $type, $s ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => $type, 'isInner' => false, 'settings' => $s, 'elements' => array() ); };
$h       = function ( $text, $tag, $class, $url = '' ) use ( $wid, $link ) { $s = array( 'title' => $text, 'header_size' => $tag, '_css_classes' => $class ); if ( $url ) { $s['link'] = $link( $url ); } return $wid( 'heading', $s ); };
$txt     = function ( $html, $class ) use ( $wid ) { return $wid( 'text-editor', array( 'editor' => $html, '_css_classes' => $class ) ); };
$btn     = function ( $text, $url, $class = 'rvl-btn' ) use ( $wid, $link ) { return $wid( 'button', array( 'text' => $text, 'link' => $link( $url ), '_css_classes' => $class ) ); };
$find    = function ( $key, $type ) { $p = get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'meta_key' => '_rvl_key', 'meta_value' => $key, 'numberposts' => 1 ) ); return $p ? $p[0]->ID : 0; };
$has     = function ( $el, $class ) { $c = $el['settings']['css_classes'] ?? ( $el['settings']['_css_classes'] ?? '' ); return in_array( $class, preg_split( '/\s+/', $c ), true ); };
$log     = array();

/* ---------- Housekeeping ---------- */
update_option( 'date_format', 'j F Y' );
$hello = get_post( 1 );
if ( $hello && 'publish' === $hello->post_status && 'Hello world!' === $hello->post_title ) { wp_trash_post( 1 ); $log['hello_world'] = 'trashed'; }

/* ---------- Articles ---------- */
$disclaimer = '<p class="rvl-disclaimer">This article is general information about the law in Punjab, Pakistan, and is not legal advice. Every matter turns on its own facts. For advice on your situation, please speak with an advocate.</p>';

$articles = array(
	'article-land' => array(
		'cat'     => 'Property',
		'title'   => 'What to check before buying land in Punjab',
		'slug'    => 'what-to-check-before-buying-land-in-punjab',
		'image'   => 35,
		'excerpt' => 'A short guide to the documents and records worth reviewing before a property transaction.',
		'body'    => <<<'HTML'
<p>Buying land is often the largest transaction a family makes, and most property disputes that reach the courts could have been avoided with careful checks before any money changed hands. The points below are the ones we review first when a client brings us a property deal.</p>

<h2>1. Confirm who owns the land</h2>
<p>For agricultural and most rural land in Punjab, ownership is recorded in the revenue record. Obtain a current <strong>Fard</strong> (an extract of the record of rights) from a Punjab Land Records Authority service centre and check that the seller's name, the khewat and khatooni numbers, the khasra numbers and the size of the share all match what you are being offered.</p>
<p>For a plot in a housing scheme or society, ownership usually rests on an allotment letter and the transfer record kept by the society or development authority. Ask the society to confirm in writing who the current owner is.</p>

<h2>2. Trace the chain of title</h2>
<p>Ask for copies of the earlier registered deeds and mutations (intiqal) through which the seller acquired the land. Each step should connect to the next. Gaps, unexplained transfers, or ownership that arrived through inheritance without a sanctioned inheritance mutation are signs that more enquiry is needed.</p>

<h2>3. Check the seller and their authority to sell</h2>
<ul>
<li>Verify the seller's CNIC against the record.</li>
<li>Where the land is jointly owned, confirm that every co-owner whose share is being sold will sign, or that the seller's own share is clearly identified.</li>
<li>If someone is selling under a power of attorney, check that it is properly executed and registered, that it actually authorises a sale, and that the principal is alive and has not revoked it.</li>
<li>If any part of the land belongs to a minor, it generally cannot be sold without the permission of the court. See our guide to <a href="/understanding-the-guardianship-process/">the guardianship process</a>.</li>
</ul>

<h2>4. Look for charges and disputes</h2>
<p>Ask whether the land is mortgaged, charged to a bank, or subject to any pending case, stay order or earlier agreement to sell. Enquire with the revenue staff and, where appropriate, check court records. A seller who will not answer these questions in writing is a seller to be careful with.</p>

<h2>5. Match the paper to the ground</h2>
<p>Visit the land and make sure what you are shown on the ground is what the khasra numbers describe. Where boundaries are uncertain, ask for an official demarcation through the revenue authorities before you pay. Check access, possession and whether anyone else is occupying the land.</p>

<h2>6. Check approvals for housing schemes</h2>
<p>If you are buying in a housing scheme, confirm that the scheme is approved by the relevant development authority (in Faisalabad, the Faisalabad Development Authority) and that the plot falls within the approved layout plan.</p>

<h2>7. Complete the transfer properly</h2>
<p>A sale of immovable property should be completed by a sale deed on the correct stamp paper and registered with the Sub-Registrar, followed by mutation of the land in the revenue record in the buyer's name. Stamp duty and the applicable federal and provincial taxes, including withholding taxes, must be paid; rates change regularly, so confirm the current figures before the transaction.</p>

<h2>8. Pay with a record</h2>
<p>Pay through bank instruments rather than cash, keep receipts that identify the land, and avoid paying the full price before the checks above are complete.</p>
HTML,
	),
	'article-guardianship' => array(
		'cat'     => 'Family',
		'title'   => 'Understanding the guardianship process',
		'slug'    => 'understanding-the-guardianship-process',
		'image'   => 36,
		'excerpt' => 'An overview of how guardianship applications are made and what the court considers.',
		'body'    => <<<'HTML'
<p>Questions about who should look after a child, and who may manage a child's property, arise after a separation, a divorce or the death of a parent. In Punjab these questions are decided under the <strong>Guardians and Wards Act, 1890</strong>, and are heard by the Family Court, which exercises the powers of the guardian court.</p>

<h2>Guardianship and custody</h2>
<p>The law draws a distinction between <strong>guardianship</strong>, which concerns legal responsibility for a child's person or property, and <strong>custody</strong>, which is about who the child lives with day to day. A court may make orders about one or both, and may also set a schedule for the other parent to meet the child.</p>

<h2>Who can apply</h2>
<p>An application can be made by a person who wishes to be appointed guardian, or by a relative or friend of the child. In practice most applications are made by a parent or a close relative.</p>

<h2>What the application contains</h2>
<p>The application sets out the child's name, age and residence, the names of the child's near relatives, who currently has custody, any property the child owns, and the reasons for the order sought. It should be supported by documents such as:</p>
<ul>
<li>the child's birth certificate or NADRA registration (B-Form);</li>
<li>the CNICs of the parents and the applicant;</li>
<li>the nikahnama and, where relevant, divorce or death certificates;</li>
<li>records of schooling, health and the child's expenses.</li>
</ul>

<h2>What the court considers</h2>
<p>The <strong>welfare of the child</strong> is the court's paramount consideration. The court looks at the child's age, sex and religion, the character and capacity of the person proposed as guardian, how closely they are related to the child, the wishes of a deceased parent, and, where the child is old enough to form an intelligent preference, the child's own wishes. The court also has regard to the personal law that applies to the child.</p>

<h2>How the case proceeds</h2>
<p>After the application is filed, notice is issued to the other side, who may file a reply. The court can make interim arrangements, including a meeting schedule, while the case is pending. The parties then lead evidence and the court decides. How long this takes depends on the court's list and on how far the parties cooperate.</p>

<h2>Guardians of a child's property</h2>
<p>A guardian appointed by the court cannot sell, mortgage or otherwise transfer a child's immovable property without the court's prior permission. This protects the child, and it is also something any buyer of such property should check. See our guide on <a href="/what-to-check-before-buying-land-in-punjab/">buying land in Punjab</a>.</p>
HTML,
	),
	'article-hearing' => array(
		'cat'     => 'Civil',
		'title'   => 'Preparing for your first hearing',
		'slug'    => 'preparing-for-your-first-hearing',
		'image'   => 37,
		'excerpt' => 'Practical points on documents, timing and what to expect when you attend court.',
		'body'    => <<<'HTML'
<p>A first court date can feel daunting. Knowing what usually happens, and arriving prepared, makes the day calmer and helps your advocate present your case well.</p>

<h2>What the first hearing usually is</h2>
<p>In most civil cases the first hearing is procedural. The court checks that the other side has been served, records who has appeared, and sets dates for the next steps, such as the filing of a written reply. It is common for the case to be adjourned to a later date. A final decision on the first day is unusual, so do not be discouraged if little seems to happen.</p>

<h2>Before the day</h2>
<ul>
<li><strong>Meet your advocate</strong> beforehand and ask what will happen at this hearing and whether you need to attend in person.</li>
<li><strong>Organise your documents.</strong> Bring originals and at least two sets of photocopies, arranged in date order.</li>
<li><strong>Write a short timeline</strong> of the key dates and events in your own words. It helps your advocate and keeps your account consistent.</li>
<li><strong>Bring your CNIC</strong> and the details of any witnesses.</li>
<li><strong>Check the cause list.</strong> Your advocate's office will confirm the court and the case number; many district courts in Punjab also publish case status online.</li>
</ul>

<h2>On the day</h2>
<ul>
<li>Arrive early. Courts call cases in turn and your case may be heard at any time during the day.</li>
<li>Dress neatly and modestly, and keep your phone on silent.</li>
<li>Stay close to the courtroom and listen for your case to be called.</li>
<li>Let your advocate speak for you. If the judge asks you a question, answer briefly, politely and truthfully.</li>
<li>Do not discuss the case with the other side unless your advocate has advised you to.</li>
</ul>

<h2>After the hearing</h2>
<p>Note the next date and what needs to happen before it. Ask your advocate to explain anything you did not follow, and keep your documents together for the next hearing.</p>
HTML,
	),
);

$post_ids = array();
foreach ( $articles as $key => $a ) {
	$term = term_exists( $a['cat'], 'category' );
	$cat  = $term ? (int) $term['term_id'] : (int) wp_insert_term( $a['cat'], 'category' )['term_id'];
	$id   = $find( $key, 'post' );
	$args = array(
		'ID' => $id, 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $a['title'], 'post_name' => $a['slug'],
		'post_excerpt' => $a['excerpt'], 'post_content' => $a['body'] . $disclaimer, 'post_category' => array( $cat ), 'post_author' => 1,
	);
	$id = wp_insert_post( $args );
	update_post_meta( $id, '_rvl_key', $key );
	set_post_thumbnail( $id, $a['image'] );
	$post_ids[ $key ] = $id;
}

/* ---------- Legal pages ---------- */
$privacy_body = <<<HTML
<p><em>Last updated: {$today}</em></p>
<p>This policy explains how Royal Vision Law Associate ("we") handles personal information collected through this website.</p>
<h2>Who we are</h2>
<p>Royal Vision Law Associate is a law firm registered with the Punjab Bar Council (Reg. No. 13294/23), with chambers at 45 District Courts, Faisalabad, Punjab, Pakistan.</p>
<h2>What we collect</h2>
<p>When you send an enquiry through our contact form, we receive the details you provide: your name, email address, and, if you choose to give them, your phone number, the practice area and your message. We do not ask for, and you should not send, confidential details of your matter through the form.</p>
<h2>How we use it</h2>
<p>We use these details only to review your enquiry and to contact you about it. Enquiries are delivered to us by email.</p>
<h2>Sharing</h2>
<p>We do not sell your information. It is handled by the service providers that host this website and deliver our email, and we may disclose it where the law requires us to.</p>
<h2>Retention</h2>
<p>We keep enquiry details for as long as needed to respond to you and, if you instruct us, for as long as our professional obligations require.</p>
<h2>Cookies</h2>
<p>This website may set essential cookies needed for it to work and to keep it secure.</p>
<h2>Your choices</h2>
<p>You may ask us what information we hold about you, or ask us to correct or delete it, by contacting us.</p>
<h2>Contact</h2>
<p>45 District Courts, Faisalabad, Punjab, Pakistan. Telephone: <a href="{$tel}">{$phone}</a>.</p>
HTML;

$legal_body = <<<HTML
<h2>About the firm</h2>
<p>Royal Vision Law Associate is a law firm registered with the Punjab Bar Council under Chapter VII of the Punjab Legal Practitioners &amp; Bar Council Rules, 1974 (Ref. 13294, letter dated 31 August 2023). Advocates: Ch. Abdul Nabi Qamar and Imran Ali. Chambers: 45 District Courts, Faisalabad, Punjab, Pakistan. Telephone: <a href="{$tel}">{$phone}</a>.</p>
<h2>No legal advice</h2>
<p>The content of this website, including our articles, is general information only. It is not legal advice and should not be relied on as such. The law and its application change, and every matter depends on its own facts.</p>
<h2>No advocate-client relationship</h2>
<p>Using this website or sending us an enquiry does not create an advocate-client relationship. That relationship begins only when we have agreed in writing to act for you. Please do not send confidential information until we have confirmed that we can act.</p>
<h2>External links</h2>
<p>Where this website links to other websites, we are not responsible for their content.</p>
<h2>Copyright</h2>
<p>© 2026 Royal Vision Law Associate. All rights reserved.</p>
HTML;

$pages = array(
	'page-privacy' => array( 'id' => 3, 'title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'body' => $privacy_body, 'lead' => 'How we handle the information you share with us through this website.' ),
	'page-legal'   => array( 'id' => 0, 'title' => 'Legal Notice', 'slug' => 'legal-notice', 'body' => $legal_body, 'lead' => 'Important information about this website and the firm.' ),
	'page-insights' => array( 'id' => 0, 'title' => 'Insights', 'slug' => 'insights', 'body' => '', 'lead' => 'Practical guidance from our advocates on property, family and civil matters in Punjab.' ),
);
$page_ids = array();
foreach ( $pages as $key => $p ) {
	$id = $find( $key, 'page' ) ?: $p['id'];
	$id = wp_insert_post( array( 'ID' => $id, 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $p['title'], 'post_name' => $p['slug'], 'post_content' => $p['body'] ) );
	update_post_meta( $id, '_rvl_key', $key );
	$page_ids[ $key ] = $id;
}
update_option( 'wp_page_for_privacy_policy', $page_ids['page-privacy'] );
$u = array(
	'insights' => get_permalink( $page_ids['page-insights'] ),
	'privacy'  => get_permalink( $page_ids['page-privacy'] ),
	'legal'    => get_permalink( $page_ids['page-legal'] ),
);

/* ---------- Menu: absolute links so it works on every page ---------- */
$menu_id = wp_get_nav_menu_object( 'Royal Vision Primary' )->term_id;
foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) { wp_delete_post( $item->ID, true ); }
$pos = 1;
foreach ( array( 'Home' => $home, 'The Firm' => $home . '#firm', 'Practice Areas' => $home . '#practice', 'Our Team' => $home . '#team', 'Insights' => $u['insights'], 'Contact' => $home . '#contact' ) as $label => $url ) {
	wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $label, 'menu-item-url' => $url, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-position' => $pos++ ) );
}

/* ---------- Shared header / footer templates ---------- */
$home_doc  = \Elementor\Plugin::$instance->documents->get( $home_id, false );
$home_data = $home_doc->get_elements_data();
$abs = function ( array $els ) use ( &$abs, $home, $u ) {
	foreach ( $els as &$el ) {
		if ( isset( $el['settings']['link']['url'] ) ) {
			$url = $el['settings']['link']['url'];
			$label = $el['settings']['title'] ?? '';
			if ( 'Insights' === $label || '#insights' === $url && 'Insights' === $label ) { $url = $u['insights']; }
			elseif ( 'Privacy Policy' === $label ) { $url = $u['privacy']; }
			elseif ( 'Legal Notice' === $label ) { $url = $u['legal']; }
			elseif ( 0 === strpos( $url, '#' ) ) { $url = '#top' === $url ? $home : $home . $url; }
			$el['settings']['link']['url'] = $url;
		}
		$el['elements'] = $abs( $el['elements'] );
	}
	return $els;
};
$parts = array();
foreach ( $home_data as $el ) {
	if ( 'container' !== $el['elType'] ) { continue; }
	if ( $has( $el, 'rvl-header' ) ) { $parts['header'] = $el; }
	if ( $has( $el, 'rvl-footer' ) ) { $parts['footer'] = $el; }
	if ( $has( $el, 'rvl-sticky' ) || $has( $el, 'rvl-footer-wrap' ) ) {
		foreach ( $el['elements'] as $w ) {
			if ( 'template' === ( $w['widgetType'] ?? '' ) ) { $parts[ $has( $el, 'rvl-sticky' ) ? 'header_tpl' : 'footer_tpl' ] = (int) $w['settings']['template_id']; }
		}
	}
}
$tpl_ids = array();
foreach ( array( 'header' => 'RV Header', 'footer' => 'RV Footer' ) as $part => $title ) {
	$id = $find( 'tpl-' . $part, 'elementor_library' );
	if ( ! $id ) {
		$tdoc = \Elementor\Plugin::$instance->documents->create( 'container', array( 'post_title' => $title, 'post_status' => 'publish' ) );
		$id   = $tdoc->get_main_id();
		update_post_meta( $id, '_rvl_key', 'tpl-' . $part );
	}
	if ( isset( $parts[ $part ] ) ) {
		$el = $parts[ $part ];
		$el['isInner'] = false;
		$el = $abs( array( $el ) )[0];
		\Elementor\Plugin::$instance->documents->get( $id, false )->save( array( 'elements' => array( $el ) ) );
	}
	$tpl_ids[ $part ] = $id;
}
$tpl_widget = function ( $part ) use ( $wid, $tpl_ids ) { return $wid( 'template', array( 'template_id' => (string) $tpl_ids[ $part ] ) ); };
$header_wrap = function () use ( $con, $tpl_widget ) {
	return $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ), 'css_classes' => 'rvl-sticky' ), array( $tpl_widget( 'header' ) ), false );
};
$footer_wrap = function () use ( $con, $tpl_widget ) {
	return $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ), 'css_classes' => 'rvl-footer-wrap' ), array( $tpl_widget( 'footer' ) ), false );
};

/* ---------- Homepage: shared parts + real article links ---------- */
$by_title = array();
foreach ( $articles as $key => $a ) { $by_title[ $a['title'] ] = array( 'url' => get_permalink( $post_ids[ $key ] ), 'date' => get_the_date( 'j F Y', $post_ids[ $key ] ) ); }
$link_articles = function ( array $els ) use ( &$link_articles, $has, $by_title, $link, $u ) {
	foreach ( $els as &$el ) {
		if ( 'container' === $el['elType'] && $has( $el, 'rvl-article' ) ) {
			$title = '';
			foreach ( $el['elements'] as $c ) { if ( 'heading' === ( $c['widgetType'] ?? '' ) && $has( $c, 'rvl-h3' ) ) { $title = $c['settings']['title']; } }
			if ( isset( $by_title[ $title ] ) ) {
				$a = $by_title[ $title ];
				foreach ( $el['elements'] as &$c ) {
					if ( $has( $c, 'rvl-slot-thumb' ) ) { $c['settings']['html_tag'] = 'a'; $c['settings']['link'] = $link( $a['url'] ); }
					if ( 'heading' === ( $c['widgetType'] ?? '' ) && ( $has( $c, 'rvl-h3' ) || $has( $c, 'rvl-inline-link' ) ) ) { $c['settings']['link'] = $link( $a['url'] ); }
					if ( $has( $c, 'rvl-meta-wrap' ) && preg_match( '#rvl-cat">([^<]+)#', $c['settings']['editor'], $m ) ) {
						$c['settings']['editor'] = '<p class="rvl-meta"><span class="rvl-cat">' . $m[1] . '</span><span class="rvl-date">' . $a['date'] . '</span></p>';
					}
				}
				unset( $c );
			}
		}
		if ( 'heading' === ( $el['widgetType'] ?? '' ) && false !== strpos( $el['settings']['title'] ?? '', 'All Insights' ) ) { $el['settings']['link'] = $link( $u['insights'] ); }
		$el['elements'] = $link_articles( $el['elements'] );
	}
	return $els;
};
$new_home = array();
foreach ( $home_data as $el ) {
	if ( $has( $el, 'rvl-header' ) || $has( $el, 'rvl-sticky' ) ) { $new_home[] = $header_wrap(); continue; }
	if ( $has( $el, 'rvl-footer' ) || $has( $el, 'rvl-footer-wrap' ) ) { $new_home[] = $footer_wrap(); continue; }
	$new_home[] = $el;
}
$new_home = $link_articles( $new_home );

/* ---------- Article / document page layout ---------- */
$section = function ( $class, $children, $extra = array() ) use ( $con, $px, $pad, $gap ) {
	return $con( array_merge( array(
		'content_width' => 'boxed', 'boxed_width' => $px( 1200 ), 'flex_direction' => 'column', 'flex_gap' => $gap( 24 ),
		'padding' => $pad( 72, 40, 72, 40 ), 'padding_tablet' => $pad( 60, 32, 60, 32 ), 'padding_mobile' => $pad( 44, 20, 44, 20 ),
		'css_classes' => 'rvl-section ' . $class,
	), $extra ), $children, false );
};
$box = function ( $class, $children, $extra = array() ) use ( $con ) {
	return $con( array_merge( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ), 'css_classes' => $class ), $extra ), $children );
};
$doc_page = function ( $eyebrow, $title, $lead, $body_els ) use ( $header_wrap, $footer_wrap, $section, $box, $h, $txt, $gap ) {
	return array(
		$header_wrap(),
		$section( 'rvl-alt rvl-bb rvl-page-head', array( $box( 'rvl-measure', array(
			$h( $eyebrow, 'div', 'rvl-eyebrow' ),
			$h( $title, 'h1', 'rvl-h1 rvl-h1-page' ),
			$txt( '<p>' . $lead . '</p>', 'rvl-body rvl-lead' ),
		), array( 'flex_gap' => $gap( 18 ) ) ) ) ),
		$section( 'rvl-white', $body_els ),
		$footer_wrap(),
	);
};
$cta = function () use ( $box, $h, $txt, $btn, $gap, $home ) {
	return $box( 'rvl-cta', array(
		$h( 'Need advice on a matter like this?', 'h2', 'rvl-h3' ),
		$txt( '<p>Tell us briefly what your matter concerns. An advocate will review your enquiry and contact you to discuss it.</p>', 'rvl-body rvl-small' ),
		$btn( 'Discuss Your Matter', $home . '#contact' ),
	), array( 'flex_gap' => $gap( 14 ), 'padding' => array( 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ) ) );
};

$save_page = function ( $id, $elements ) {
	update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	$d = \Elementor\Plugin::$instance->documents->get( $id, false );
	$d->save( array( 'elements' => $elements, 'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );
};

foreach ( $articles as $key => $a ) {
	$id   = $post_ids[ $key ];
	$img  = $a['image'];
	$body = $box( 'rvl-measure rvl-article-body', array(
		$h( '← All insights', 'div', 'rvl-more rvl-back', $u['insights'] ),
		$box( 'rvl-feature rvl-has-img', array(), array(
			'background_background' => 'classic', 'background_image' => array( 'id' => $img, 'url' => wp_get_attachment_image_url( $img, 'large' ), 'size' => '' ),
			'background_position' => 'center center', 'background_size' => 'cover', 'background_repeat' => 'no-repeat',
		) ),
		$txt( $a['body'], 'rvl-prose' ),
		$txt( $disclaimer, 'rvl-prose rvl-note-wrap' ),
		$cta(),
	), array( 'flex_gap' => $gap( 32 ) ) );
	$save_page( $id, $doc_page( $a['cat'] . ' · ' . get_the_date( 'j F Y', $id ), $a['title'], $a['excerpt'], array( $body ) ) );
}
foreach ( array( 'page-privacy' => 'Privacy Policy', 'page-legal' => 'Legal Notice' ) as $key => $title ) {
	$save_page( $page_ids[ $key ], $doc_page( 'Royal Vision Law Associate', $title, $pages[ $key ]['lead'], array(
		$box( 'rvl-measure', array( $txt( $pages[ $key ]['body'], 'rvl-prose' ) ) ),
	) ) );
}
$posts_widget = $wid( 'posts', array(
	'_skin' => 'classic', 'posts_post_type' => 'post', 'classic_posts_per_page' => 9,
	'classic_columns' => '3', 'classic_columns_tablet' => '2', 'classic_columns_mobile' => '1',
	'classic_thumbnail' => 'top', 'classic_thumbnail_size_size' => 'medium_large', 'classic_item_ratio' => array( 'unit' => 'px', 'size' => 0.62, 'sizes' => array() ),
	'classic_show_title' => 'yes', 'classic_title_tag' => 'h3', 'classic_show_excerpt' => 'yes', 'classic_excerpt_length' => 25,
	'classic_meta_data' => array( 'date' ), 'classic_show_read_more' => 'yes', 'classic_read_more_text' => 'Read article →',
	'classic_column_gap' => $px( 40 ), 'classic_row_gap' => $px( 48 ), '_css_classes' => 'rvl-posts',
) );
$save_page( $page_ids['page-insights'], $doc_page( 'Insights', 'Updates and guidance', $pages['page-insights']['lead'], array( $posts_widget ) ) );

/* ---------- CSS: move to Site Settings so every page shares it ---------- */
$home_settings = $home_doc->get_settings();
$kit_id  = (int) get_option( 'elementor_active_kit' );
$kit_doc = \Elementor\Plugin::$instance->documents->get( $kit_id, false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$css = ! empty( $home_settings['custom_css'] ) ? $home_settings['custom_css'] : ( $kit_set['custom_css'] ?? '' );
$css = str_replace( 'body.admin-bar selector .rvl-header,.admin-bar .rvl-header{top:32px}', '.admin-bar .rvl-sticky{top:32px}', $css );
$css = str_replace( 'selector .rvl-header{position:sticky;top:0;z-index:50;', 'selector .rvl-header{', $css );
$add = <<<'CSS'

/* Shared layout: sticky header wrapper, article and document pages */
selector .rvl-sticky{position:sticky;top:0;z-index:50}
selector .rvl-sticky .elementor-widget-template,selector .rvl-footer-wrap .elementor-widget-template{width:100%}
selector .rvl-measure{max-width:760px}
selector .rvl-h1-page .elementor-heading-title{font-size:clamp(30px,3.6vw,46px)}
selector .rvl-back .elementor-heading-title{font-size:15px}
selector .rvl-feature{aspect-ratio:16/9;width:100%;border-radius:2px}
selector .rvl-prose{font-family:var(--rvl-sans);font-size:17px;line-height:1.75;color:var(--rvl-text)}
selector .rvl-prose p{margin:0 0 1.1em}
selector .rvl-prose h2{font-family:var(--rvl-serif);font-weight:400;font-size:clamp(22px,2.2vw,26px);line-height:1.3;color:var(--rvl-ink);margin:1.8em 0 .6em}
selector .rvl-prose h2:first-child{margin-top:0}
selector .rvl-prose ul,selector .rvl-prose ol{margin:0 0 1.2em;padding-left:1.25em}
selector .rvl-prose li{margin-bottom:.5em}
selector .rvl-prose li::marker{color:var(--rvl-label)}
selector .rvl-prose strong{color:var(--rvl-ink);font-weight:600}
selector .rvl-prose a{color:var(--rvl-green);text-decoration:underline;text-underline-offset:3px}
selector .rvl-prose a:hover{color:var(--rvl-ink)}
selector .rvl-prose em{color:var(--rvl-muted)}
selector .rvl-disclaimer{font-size:15px;line-height:1.6;color:var(--rvl-muted);padding:14px 16px;background:var(--rvl-stone);border-left:2px solid var(--rvl-line);margin:0}
selector .rvl-cta{background:var(--rvl-stone);border:1px solid var(--rvl-line);border-radius:2px}
selector .rvl-slot-thumb{transition:opacity .15s ease}
selector a.rvl-slot-thumb:hover{opacity:.9}
selector .rvl-h3 .elementor-heading-title a{color:inherit}
selector .rvl-h3 .elementor-heading-title a:hover{color:var(--rvl-green)}

/* Insights listing (Posts widget) */
selector .rvl-posts .elementor-post__thumbnail{border-radius:2px}
selector .rvl-posts .elementor-post__text{padding-top:16px}
selector .rvl-posts .elementor-post__title,selector .rvl-posts .elementor-post__title a{font-family:var(--rvl-serif);font-weight:400;font-size:20px;line-height:1.35;color:var(--rvl-ink)}
selector .rvl-posts .elementor-post__title a:hover{color:var(--rvl-green)}
selector .rvl-posts .elementor-post__meta-data{font-family:var(--rvl-sans);font-size:13.5px;color:var(--rvl-muted);margin-top:8px}
selector .rvl-posts .elementor-post__excerpt p{font-family:var(--rvl-sans);font-size:16px;line-height:1.6;color:var(--rvl-text);margin-top:10px}
selector .rvl-posts .elementor-post__read-more{font-family:var(--rvl-sans);font-size:15px;font-weight:600;color:var(--rvl-green)}
selector .rvl-posts .elementor-post__read-more:hover{color:var(--rvl-ink)}

/* Insights listing: fixed 16:10 thumbnails without relying on JS */
selector .rvl-posts .elementor-post__thumbnail__link{display:block;width:100%;margin-bottom:0}
selector .rvl-posts .elementor-post__thumbnail{position:relative;aspect-ratio:16/10;padding-bottom:0!important;overflow:hidden;background:var(--rvl-tile)}
selector .rvl-posts .elementor-post__thumbnail img{position:absolute;inset:0;width:100%!important;height:100%!important;object-fit:cover;transform:none!important}
selector .rvl-posts .elementor-post{align-self:start}
CSS;
if ( false === strpos( $css, '/* Shared layout:' ) ) { $css .= $add; }
$kit_set['custom_css'] = $css;
$kit_doc->save( array( 'settings' => $kit_set ) );

$home_doc->save( array( 'elements' => $new_home, 'settings' => array( 'custom_css' => '', 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );

return array(
	'posts'     => array_map( 'get_permalink', $post_ids ),
	'pages'     => array_map( 'get_permalink', $page_ids ),
	'templates' => $tpl_ids,
	'home_top'  => count( $new_home ),
	'kit_css'   => strlen( $css ),
	'log'       => $log,
);
