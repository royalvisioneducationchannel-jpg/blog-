<?php
/** Phase 04 builder: public pages in Elementor (Flexbox Containers). Idempotent by slug. */
return (function () {
    if (home_url() !== 'https://lightyellow-alligator-410734.hostingersite.com') { return 'WRONG SITE'; }
    $E = \Elementor\Plugin::$instance;
    $U = static fn($p) => home_url($p);
    $icons = get_theme_file_uri('assets/icons/');
    $assets = plugins_url('assets/', WP_PLUGIN_DIR . '/seller-tools/seller-tools.php');
    $id = static fn() => substr(md5(uniqid('', true) . mt_rand()), 0, 7);
    $box = static fn($t, $r = null, $b = null, $l = null) => ['unit' => 'px', 'top' => (string) $t, 'right' => (string) ($r ?? $t), 'bottom' => (string) ($b ?? $t), 'left' => (string) ($l ?? $r ?? $t), 'isLinked' => false];
    $gap = static fn($n) => ['unit' => 'px', 'size' => $n, 'column' => (string) $n, 'row' => (string) $n];
    $font = static fn($fam, $w, $size, $mobile = null) => ['typography_typography' => 'custom', 'typography_font_family' => $fam, 'typography_font_weight' => (string) $w, 'typography_font_size' => ['unit' => 'px', 'size' => $size, 'sizes' => []]] + ($mobile ? ['typography_font_size_mobile' => ['unit' => 'px', 'size' => $mobile, 'sizes' => []]] : []);
    $c = static fn(array $s, array $kids) => ['id' => $id(), 'elType' => 'container', 'isInner' => false, 'settings' => $s + ['content_width' => 'full', 'padding' => $box(0)], 'elements' => $kids];
    $w = static fn(string $type, array $s) => ['id' => $id(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $s, 'elements' => []];
    $h = static fn($text, $tag = 'h2', $size = 30, $color = 'text', $mobile = null) => $w('heading', ['title' => $text, 'header_size' => $tag, '__globals__' => ['title_color' => 'globals/colors?id=' . $color]] + $font('Poppins', $tag === 'h1' ? 700 : 600, $size, $mobile));
    $p = static fn($html, $size = 16, $color = 'secondary') => $w('text-editor', ['editor' => $html, '__globals__' => ['text_color' => 'globals/colors?id=' . $color]] + $font('Arial', 400, $size));
    $btn = static function ($label, $url, $primary = true) use ($w, $box, $font) {
        $s = ['text' => $label, 'link' => ['url' => $url], 'border_radius' => $box(8), 'text_padding' => $box(13, 22)] + $font('Poppins', 600, 15);
        $s += $primary ? ['button_text_color' => '#FFFFFF', '__globals__' => ['background_color' => 'globals/colors?id=primary', 'button_background_hover_color' => 'globals/colors?id=stprdk']]
                       : ['background_color' => '#FFFFFF', 'border_border' => 'solid', 'border_width' => $box(1), '__globals__' => ['button_text_color' => 'globals/colors?id=text', 'border_color' => 'globals/colors?id=stinpt']];
        return $w('button', $s);
    };
    $sc = static fn($code) => $w('shortcode', ['shortcode' => $code]);
    $img = static fn($url, $width = null, $link = '') => $w('image', ['image' => ['url' => $url, 'id' => ''], 'image_size' => 'full', 'width' => $width ? ['unit' => 'px', 'size' => $width, 'sizes' => []] : ['unit' => '%', 'size' => 100, 'sizes' => []], 'link_to' => $link ? 'custom' : 'none', 'link' => ['url' => $link]]);
    $section = static fn(array $kids, bool $alt = false, int $pad = 64) => $c(['content_width' => 'boxed', 'flex_direction' => 'column', 'flex_gap' => $gap(20), 'padding' => $box($pad, 24), 'padding_mobile' => $box(min($pad, 48), 16), 'background_background' => 'classic', '__globals__' => ['background_color' => 'globals/colors?id=' . ($alt ? 'stsurf' : 'stwhite')]], $kids);
    $row = static fn(array $kids, int $g = 16, string $justify = 'flex-start') => $c(['flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_gap' => $gap($g), 'flex_justify_content' => $justify, 'flex_align_items' => 'stretch'], $kids);
    $card = static fn(array $kids, $basis = 300) => $c(['flex_direction' => 'column', 'flex_gap' => $gap(10), 'padding' => $box(24), 'border_border' => 'solid', 'border_width' => $box(1), 'border_radius' => $box(12), 'background_background' => 'classic', 'custom_css' => "selector{flex:1 1 {$basis}px}", '__globals__' => ['border_color' => 'globals/colors?id=stbord', 'background_color' => 'globals/colors?id=stwhite']], $kids);
    $note = static fn($html, $icon = 'lock', $warn = false) => $c(['flex_direction' => 'row', 'flex_align_items' => 'flex-start', 'flex_gap' => $gap(12), 'padding' => $box(14, 16), 'border_radius' => $box(10), 'background_background' => 'classic', 'background_color' => $warn ? '#FFF6DF' : '#F6F7F9'], [$img($icons . $icon . '.svg', 20), $p($html, 15, 'text')]);
    $faq = static fn(array $items) => $w('accordion', ['tabs' => array_map(fn($q) => ['_id' => $id(), 'tab_title' => $q[0], 'tab_content' => '<p>' . $q[1] . '</p>'], $items), 'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Poppins', 'title_typography_font_weight' => '600', '__globals__' => ['title_color' => 'globals/colors?id=text', 'tab_active_color' => 'globals/colors?id=primary', 'content_color' => 'globals/colors?id=secondary', 'border_color' => 'globals/colors?id=stbord']]);
    $cta = static fn($title, $text, $label, $url) => $c(['content_width' => 'boxed', 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $gap(16), 'padding' => $box(32), 'border_radius' => $box(12), 'background_background' => 'classic', '__globals__' => ['background_color' => 'globals/colors?id=sttint']], [$c(['flex_direction' => 'column', 'flex_gap' => $gap(6), 'custom_css' => 'selector{flex:1 1 320px}'], [$h($title, 'h2', 24), $p('<p>' . $text . '</p>')]), $btn($label, $url)]);
    $pageHead = static fn($title, $lead) => $section([$h($title, 'h1', 44, 'text', 32), $p('<p>' . $lead . '</p>', 18)], false, 48);
    $faqCommon = [
        ['Are my photos uploaded?', 'No. Resize, compress, square maker, bulk and watermark run on your device in the browser. Only the background remover (not available yet) would send the photo you choose to a removal service, and it says so before you click.'],
        ['What are the limits of the free tools?', 'Up to 50 photos per batch, 20 MB per photo and 100 MB per batch. Output is limited to 16 megapixels (8,192 px per side). These limits run in your browser.'],
        ['Do I need an account?', 'No. Free tools work without signup. Accounts will be needed later for saved presets and paid plans.'],
        ['Can I buy a plan today?', 'Not yet. Paid plans are in development and prices are awaiting approval, so checkout is switched off.'],
    ];

    $tools = ST_Registry::all();
    $pages = [];
    // Home
    $pages['home'] = [9, 'Seller Toolkit', null, [
        $section([$row([
            $c(['flex_direction' => 'column', 'flex_gap' => $gap(16), 'flex_justify_content' => 'center', 'custom_css' => 'selector{flex:1 1 420px}'], [
                $h('For online sellers in Pakistan', 'p', 13, 'primary'),
                $h('Tools and a workspace for your selling day.', 'h1', 52, 'text', 34),
                $p('<p>Prepare product photos for Daraz, Shopify, Amazon and Etsy listings, work out profit per order, and make a clean invoice — free, in your browser.</p>', 18),
                $row([$btn('Try free tools', $U('/tools/')), $btn('Compare plans', $U('/pricing/'), false)], 12),
                $w('icon-list', ['view' => 'inline', 'icon_list' => array_map(fn($t) => ['_id' => $id(), 'text' => $t, 'selected_icon' => ['value' => 'fas fa-check', 'library' => 'fa-solid']], ['No signup for free tools', 'Photos processed on your device', 'Up to 50 photos per batch']), '__globals__' => ['icon_color' => 'globals/colors?id=accent', 'text_color' => 'globals/colors?id=secondary']]),
            ]),
            $c(['flex_direction' => 'column', 'flex_gap' => $gap(8), 'custom_css' => 'selector{flex:1 1 480px}selector img{border:1px solid #DFE3E9;border-radius:14px;box-shadow:0 12px 40px rgba(32,37,43,.10)}'], [
                $w('image', ['image' => ['url' => wp_get_attachment_url(67), 'id' => 67], 'image_size' => 'large']),
                $p('<p>Real studio screen with demo photos.</p>', 13),
            ]),
        ], 48)], false, 56),
        $section([$h('Start from your marketplace'), $p('<p>Editable canvas presets. Check each marketplace’s current image guidelines for your category. Seller Toolkit is not affiliated with these marketplaces.</p>'),
            $row(array_map(fn($m) => $c(['flex_direction' => 'row', 'flex_align_items' => 'center', 'flex_gap' => $gap(10), 'padding' => $box(14, 18), 'border_border' => 'solid', 'border_width' => $box(1), 'border_radius' => $box(10), 'background_background' => 'classic', 'background_color' => '#FFFFFF', 'custom_css' => 'selector{flex:0 1 auto}selector img{height:22px;width:auto}', '__globals__' => ['border_color' => 'globals/colors?id=stbord']], [$img($assets . $m[1], null, $U('/tools/image-resizer/')), $p('<p><a href="' . esc_url($U('/tools/image-resizer/')) . '">' . $m[0] . ' preset</a></p>', 15, 'text')]), [['Daraz', 'daraz.svg'], ['Shopify', 'shopify.svg'], ['Amazon', 'amazon.png'], ['Etsy', 'etsy.svg']]), 12),
        ], true),
        $section([$h('Free tools you can use today'), $p('<p>Nine tools. Eight run fully in your browser.</p>'), $sc('[seller_tools_directory]')]),
        $section([$h('Plans'), $p('<p>Free stays free. Paid plans add saved work and business tools when they are ready. Prices in PKR are pending approval — nothing can be bought yet.</p>'), $sc('[seller_tools_pricing table="0"]'), $p('<p><a href="' . esc_url($U('/pricing/')) . '">Compare plans in detail →</a></p>', 15, 'primary')], true),
        $section([$h('How it works'), $row([
            $card([$h('1. Choose a task', 'h3', 18), $p('<p>Pick a marketplace size, compress, make a square, or add your logo.</p>', 15)]),
            $card([$h('2. Check the result', 'h3', 18), $p('<p>See dimensions and file size before downloading.</p>', 15)]),
            $card([$h('3. Download', 'h3', 18), $p('<p>One photo, or a ZIP of the whole batch.</p>', 15)]),
        ]), $p('<p><a href="' . esc_url($U('/how-it-works/')) . '">More about how it works →</a></p>', 15)]),
        $section([$h('Questions'), $faq($faqCommon), $p('<p>More answers in <a href="' . esc_url($U('/help/')) . '">Help</a>.</p>', 15)], true),
        $section([$cta('Prepare your next listing photos', 'Free, in your browser. No signup needed.', 'Try free tools', $U('/tools/'))]),
    ]];
    $pages['tools'] = [10, 'All tools', null, [$pageHead('All tools', 'Search by task, or filter free and paid tools. Every tool opens directly — no account needed for free tools.'), $section([$sc('[seller_tools_directory]'), $note('Image tools run in your browser. Your photos are not uploaded to our server.')], false, 16)]];
    $pages['pricing'] = [null, 'Pricing', null, [
        $pageHead('Simple plans. Free tools stay free.', 'All prices in PKR. Before you pay you will see the price, billing interval, limits, renewal type and cancellation terms.'),
        $section([$note('<strong>Not on sale yet.</strong> Paid plans are in development and prices are awaiting approval. Checkout is switched off until both are ready.', 'alert', true), $sc('[seller_tools_pricing]')], false, 16),
        $section([$h('Background-removal credits', 'h2', 26), $note('<strong>Not available yet.</strong> One-time credit packs will be separate from plans, and a failed removal will not use a credit. We are moving to a new removal provider before selling credits.', 'cutout')], true, 48),
        $section([$h('Before you buy', 'h2', 26), $row([
            $card([$h('Renewal', 'h3', 17), $p('<p>Each plan will show whether it renews automatically or manually. If manual, you will see “Renew manually” and get a payment link.</p>', 15)]),
            $card([$h('Cancellation', 'h3', 17), $p('<p>Cancellation and refund terms will be published here before any plan goes on sale.</p>', 15)]),
            $card([$h('Taxes and fees', 'h3', 17), $p('<p>Shown at checkout once the payment method and tax treatment are confirmed.</p>', 15)]),
        ])], false, 48),
    ]];
    foreach (['image-pro' => ['image_pro', 'Image Pro', 'In development', 'Save the settings you use every day: marketplace presets, brand kits with your logo and watermark, and saved project settings. Your original photos stay on your device unless you deliberately choose to save them.', ['Cloud photo storage (not included at launch)', 'Background removal (sold separately as credits, when available)']],
              'seller-desk' => ['seller_desk', 'Seller Desk', 'Planned', 'A focused workspace for orders, stock, expenses, returns and courier payment matching from CSV statements. It will be sold only after it is built and tested.', ['Live courier or marketplace connections (CSV import only at first)', 'Bank-verified payment status unless you confirm it']],
              'seller-suite' => ['seller_suite', 'Seller Suite', 'Planned', 'Image Pro and Seller Desk together in one plan. Identical limits are not doubled.', ['Anything not listed in Image Pro or Seller Desk']]] as $slug => [$plan, $name, $status, $intro, $excl]) {
        $pages['pricing/' . $slug] = [null, $name, 'pricing', [
            $pageHead($name, $status . ' — not on sale yet. ' . $intro),
            $section([$sc('[seller_tools_pricing plan="' . $plan . '" table="0"]'), $h('Not included', 'h2', 22), $w('icon-list', ['icon_list' => array_map(fn($t) => ['_id' => $id(), 'text' => $t, 'selected_icon' => ['value' => 'fas fa-minus', 'library' => 'fa-solid']], $excl), '__globals__' => ['icon_color' => 'globals/colors?id=secondary', 'text_color' => 'globals/colors?id=text']]), $p('<p><a href="' . esc_url($U('/pricing/')) . '">← All plans</a></p>', 15)], false, 16),
        ]];
    }
    $pages['how-it-works'] = [null, 'How it works', null, [
        $pageHead('How it works', 'Seller Toolkit gives you focused tools for listing photos, pricing and paperwork. Here is what happens to your files and what the limits are.'),
        $section([$row([
            $card([$h('Your photos stay on your device', 'h3', 18), $p('<p>Resize, compress, square maker, bulk resize and watermark use your browser to process photos. The files are not uploaded to our server. Output files get new names and photo metadata is removed.</p>', 15)], 360),
            $card([$h('Calculators run locally too', 'h3', 18), $p('<p>The profit calculator, invoice generator and UTM builder work in your browser. Invoices print or save as PDF through your browser’s print dialog.</p>', 15)], 360),
        ]), $h('Limits', 'h2', 24), $p('<ul><li>Up to 50 photos per batch, 20 MB per photo, 100 MB per batch.</li><li>Photos up to 36 megapixels can be opened; output is limited to 16 megapixels (8,192 px per side).</li><li>JPG, PNG and WebP in and out.</li><li>These limits run in your browser and depend on your device’s memory.</li></ul>', 16, 'text'),
            $h('Background removal', 'h2', 24), $p('<p>This is the only tool that needs a server and a paid removal service. It is not available right now because our previous provider is closing its self-serve service on 1 December 2026. When it returns, you will see clearly that your chosen photo is sent to the service before you click.</p>', 16, 'text'),
            $cta('Try a tool now', 'No signup needed for free tools.', 'Open the tools', $U('/tools/'))], false, 16),
    ]];
    $guideCards = [];
    foreach ([['Prepare listing photos for a marketplace', '/tools/image-resizer/'], ['Make photos smaller without changing size', '/tools/image-compressor/'], ['Work out profit per order', '/tools/profit-calculator/'], ['Create an invoice', '/tools/invoice-generator/'], ['Track which post brought a sale', '/tools/utm-builder/']] as [$t, $u]) {
        $guideCards[] = $card([$h($t, 'h3', 17), $p('<p><a href="' . esc_url($U($u)) . '">Open the tool and its how-to →</a></p>', 15)], 300);
    }
    $pages['guides'] = [null, 'Guides', null, [
        $pageHead('Guides', 'Practical, step-by-step guides for everyday selling tasks. Our first written guides are in progress; until then, each tool page explains how to use it.'),
        $section([$h('Start with a task', 'h2', 24), $row($guideCards), $h('Guides being written', 'h2', 24), $p('<ul><li>Preparing listing photos</li><li>Choosing JPG, PNG or WebP</li><li>Pricing with packing and shipping costs</li><li>Tracking returned orders</li><li>Understanding courier statements</li></ul>', 16, 'text')], false, 16),
    ]];
    $pages['help'] = [null, 'Help', null, [
        $pageHead('Help', 'Answers about the tools, limits, privacy and plans.'),
        $section([$faq(array_merge($faqCommon, [['Which formats are supported?', 'JPG, PNG and WebP. GIF and other formats are not supported.'], ['Why did a large photo fail in the compressor?', 'The compressor keeps your photo’s dimensions, and output is limited to 16 megapixels. For larger photos, resize first. We are working on an option to limit the longest side.'], ['Which browsers are supported?', 'We test in current Chromium-based browsers (Chrome, Edge). Firefox and Safari testing is in progress.'], ['How do I contact support?', 'Contact details are coming soon. See the Contact page.']])), $p('<p><a href="' . esc_url($U('/contact/')) . '">Contact →</a></p>', 15)], false, 16),
    ]];
    $pages['contact'] = [null, 'Contact', null, [
        $pageHead('Contact', 'Support contact details are coming soon.'),
        $section([$note('We will publish a support email and expected response times here before launch. Until then, the <a href="' . esc_url($U('/help/')) . '">Help page</a> answers common questions.', 'help'), $p('<p>We do not use a contact form yet, so we don’t collect your personal details on this page.</p>', 15)], false, 16),
    ]];
    // Tool pages: keep their shortcode and any existing guide text.
    foreach ([12, 13, 25, 26, 27, 28, 29, 11, 14, 15] as $pid) {
        $post = get_post($pid);
        preg_match('/\[(seller_tool[^\]]*|seller_image_studio)\]/', $post->post_content, $m);
        $shortcode = $m[0] ?? '';
        $extra = trim(str_replace($shortcode, '', $post->post_content));
        $tid = $post->post_name;
        $t = $tools[$tid] ?? null;
        $studio = $pid === 29 || ($t && $t['category'] === 'images');
        $kids = [];
        if (!$studio) { $kids[] = $pageHead($post->post_title, $t ? $t['description'] : ''); }
        $body = [$sc($shortcode)];
        if ($extra !== '') { $body[] = $p($extra, 16, 'text'); }
        $body[] = $note($t && $t['processing'] === 'server' ? 'This tool sends the photo you choose to a removal service. It is not available yet.' : 'Runs in your browser. Nothing you enter or upload here is sent to our server.', $t && $t['processing'] === 'server' ? 'cutout' : 'lock');
        $body[] = $p('<p>Need help? See <a href="' . esc_url($U('/help/')) . '">Help</a> or <a href="' . esc_url($U('/tools/')) . '">browse all tools</a>.</p>', 15);
        $kids[] = $section($body, false, $studio ? 24 : 16);
        $pages['__tool_' . $pid] = [$pid, $post->post_title, null, $kids];
    }

    $report = [];
    foreach ($pages as $slug => [$pid, $title, $parentSlug, $elements]) {
        if (!$pid) {
            $existing = get_page_by_path($slug);
            if ($existing) { $pid = $existing->ID; }
            else {
                $parent = $parentSlug ? (get_page_by_path($parentSlug)->ID ?? 0) : 0;
                $pid = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => basename($slug), 'post_parent' => $parent]);
            }
        }
        update_post_meta($pid, '_elementor_edit_mode', 'builder');
        update_post_meta($pid, '_elementor_template_type', 'wp-page');
        update_post_meta($pid, '_wp_page_template', 'elementor_header_footer');
        $doc = $E->documents->get($pid, false);
        $doc->save(['elements' => $elements, 'settings' => ['template' => 'elementor_header_footer', 'hide_title' => 'yes']]);
        $report[$slug] = $pid . ' ' . get_permalink($pid);
    }

    // Menu: Tools (with tool sub-items), Pricing, How it works, Guides, Help.
    $menu = wp_get_nav_menu_object('st-primary');
    foreach (wp_get_nav_menu_items($menu->term_id) ?: [] as $item) { wp_delete_post($item->ID, true); }
    $add = static fn($title, $pid, $parent = 0, $pos = 0) => wp_update_nav_menu_item($menu->term_id, 0, ['menu-item-title' => $title, 'menu-item-object' => 'page', 'menu-item-object-id' => $pid, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish', 'menu-item-parent-id' => $parent, 'menu-item-position' => $pos]);
    $top = $add('Tools', 10, 0, 1); $i = 2;
    $add('All tools', 10, $top, $i++);
    foreach ([29, 12, 13, 25, 26, 27, 11, 14, 15] as $pid) { $add(get_the_title($pid), $pid, $top, $i++); }
    foreach (['pricing' => 'Pricing', 'how-it-works' => 'How it works', 'guides' => 'Guides', 'help' => 'Help'] as $slug => $label) { $add($label, get_page_by_path($slug)->ID, 0, $i++); }
    $report['menu_items'] = count(wp_get_nav_menu_items($menu->term_id));

    // Footer links.
    $fdoc = $E->documents->get(41, false); $fdata = $fdoc->get_elements_data();
    $walk = function (array &$els) use (&$walk, $id, $U) { foreach ($els as &$el) { if (($el['widgetType'] ?? '') === 'icon-list') { $el['settings']['icon_list'] = array_map(fn($l) => ['_id' => $id(), 'text' => $l[0], 'selected_icon' => ['value' => '', 'library' => ''], 'link' => ['url' => $U($l[1])]], [['All tools', '/tools/'], ['Pricing', '/pricing/'], ['How it works', '/how-it-works/'], ['Guides', '/guides/'], ['Help', '/help/'], ['Contact', '/contact/']]); } if (!empty($el['elements'])) { $walk($el['elements']); } } unset($el); };
    $walk($fdata); $fdoc->save(['elements' => $fdata]);
    $E->files_manager->clear_cache();
    ST_Audit::log('site.pages.built', 'pages', 'phase04', ['pages' => count($pages)]);
    return $report;
})();
