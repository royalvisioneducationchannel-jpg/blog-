<?php
/**
 * One-off builder (Phase 03): creates Seller Toolkit Elementor templates with Flexbox Containers.
 * Idempotent: skips templates that already exist by title. Returns a report array.
 */
return (function () {
    if (home_url() !== 'https://lightyellow-alligator-410734.hostingersite.com') { return 'WRONG SITE'; }
    $E = \Elementor\Plugin::$instance;
    $widgets = array_keys($E->widgets_manager->get_widget_types());
    $types = array_keys($E->documents->get_document_types());
    $theme = get_theme_file_uri('assets/');
    $home = home_url('/');
    $id = static fn() => substr(md5(uniqid('', true) . mt_rand()), 0, 7);
    $box = static fn($t, $r = null, $b = null, $l = null) => ['unit' => 'px', 'top' => (string) $t, 'right' => (string) ($r ?? $t), 'bottom' => (string) ($b ?? $t), 'left' => (string) ($l ?? $r ?? $t), 'isLinked' => false];
    $font = static fn($fam, $w, $size) => ['typography_typography' => 'custom', 'typography_font_family' => $fam, 'typography_font_weight' => (string) $w, 'typography_font_size' => ['unit' => 'px', 'size' => $size, 'sizes' => []]];
    $c = static function (array $settings, array $children) use ($id) {
        return ['id' => $id(), 'elType' => 'container', 'isInner' => false, 'settings' => $settings + ['content_width' => 'full'], 'elements' => $children];
    };
    $w = static fn(string $type, array $settings) => ['id' => $id(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => []];
    $heading = static fn($text, $tag, $size, $link = null, $color = 'text') => $w('heading', ['title' => $text, 'header_size' => $tag, 'link' => $link ? ['url' => $link] : ['url' => ''], '__globals__' => ['title_color' => 'globals/colors?id=' . $color]] + $font('Poppins', 600, $size));
    $text = static fn($html, $color = 'secondary', $size = 15) => $w('text-editor', ['editor' => $html, '__globals__' => ['text_color' => 'globals/colors?id=' . $color]] + $font('Arial', 400, $size));
    $img = static fn($file, $px, $link = null) => $w('image', ['image' => ['url' => $theme . $file, 'id' => ''], 'image_size' => 'full', 'width' => ['unit' => 'px', 'size' => $px, 'sizes' => []], 'link_to' => $link ? 'custom' : 'none', 'link' => ['url' => (string) $link]]);
    $button = static function ($label, $url, $style = 'primary') use ($w, $box, $font) {
        $s = ['text' => $label, 'link' => ['url' => $url], 'size' => 'sm', 'border_radius' => $box(8)] + $font('Poppins', 600, 15);
        if ($style === 'primary') { $s += ['button_text_color' => '#FFFFFF', '__globals__' => ['background_color' => 'globals/colors?id=primary', 'button_background_hover_color' => 'globals/colors?id=stprdk']]; }
        elseif ($style === 'link') { $s += ['background_color' => 'transparent', '__globals__' => ['button_text_color' => 'globals/colors?id=primary'], 'text_padding' => $box(0)]; }
        else { $s += ['__globals__' => ['background_color' => 'globals/colors?id=stsurf', 'button_text_color' => 'globals/colors?id=secondary']]; }
        return $w('button', $s);
    };
    $card = ['flex_direction' => 'column', 'flex_gap' => ['unit' => 'px', 'size' => 12, 'column' => '12', 'row' => '12'], 'padding' => $box(24), 'border_border' => 'solid', 'border_width' => $box(1), 'border_radius' => $box(12), 'background_background' => 'classic', '__globals__' => ['border_color' => 'globals/colors?id=stbord', 'background_color' => 'globals/colors?id=stwhite']];

    $brand = $c(['flex_direction' => 'row', 'flex_align_items' => 'center', 'flex_gap' => ['unit' => 'px', 'size' => 10, 'column' => '10', 'row' => '10'], 'width' => ['unit' => '%', 'size' => ''], 'content_width' => 'full'], [
        $img('logo-mark.svg', 32, $home), $heading('Seller Toolkit', 'div', 18, $home),
    ]);
    $nav = in_array('nav-menu', $widgets, true)
        ? $w('nav-menu', ['menu' => 'st-primary', 'layout' => 'horizontal', 'align_items' => 'center', 'pointer' => 'none', 'dropdown' => 'tablet', 'toggle' => 'burger', 'full_width' => 'yes', '__globals__' => ['color_menu_item' => 'globals/colors?id=text', 'color_menu_item_hover' => 'globals/colors?id=primary']] + ['menu_typography_typography' => 'custom', 'menu_typography_font_family' => 'Arial', 'menu_typography_font_size' => ['unit' => 'px', 'size' => 15, 'sizes' => []]])
        : $text('<a href="' . esc_url(home_url('/tools/')) . '">All tools</a>');

    $docs = [
        ['header', 'ST · Site header', [$c(['html_tag' => 'header', 'content_width' => 'boxed', 'flex_direction' => 'row', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_wrap' => 'nowrap', 'min_height' => ['unit' => 'px', 'size' => 64, 'sizes' => []], 'padding' => $box(0, 24), 'border_border' => 'solid', 'border_width' => $box(0, 0, 1, 0), 'background_background' => 'classic', '__globals__' => ['border_color' => 'globals/colors?id=stbord', 'background_color' => 'globals/colors?id=stwhite']], [
            $brand, $nav, $button('Open Studio', home_url('/tools/product-image-tools/')),
        ])], [['type' => 'include', 'name' => 'general', 'sub_name' => '', 'sub_id' => '']]],
        ['footer', 'ST · Site footer', [$c(['html_tag' => 'footer', 'content_width' => 'boxed', 'flex_direction' => 'column', 'flex_gap' => ['unit' => 'px', 'size' => 24, 'column' => '24', 'row' => '24'], 'padding' => $box(48, 24, 32, 24), 'border_border' => 'solid', 'border_width' => $box(1, 0, 0, 0), '__globals__' => ['border_color' => 'globals/colors?id=stbord']], [
            $c(['flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_justify_content' => 'space-between', 'flex_gap' => ['unit' => 'px', 'size' => 24, 'column' => '24', 'row' => '24']], [
                $c(['flex_direction' => 'column', 'flex_gap' => ['unit' => 'px', 'size' => 10, 'column' => '10', 'row' => '10'], 'width' => ['unit' => 'px', 'size' => 360, 'sizes' => []]], [
                    $brand, $text('<p>Tools for online sellers in Pakistan. Image tools run in your browser — your photos are not uploaded.</p>', 'secondary', 14),
                ]),
                in_array('icon-list', $widgets, true) ? $w('icon-list', ['icon_list' => array_map(fn($l) => ['_id' => $id(), 'text' => $l[0], 'selected_icon' => ['value' => '', 'library' => ''], 'link' => ['url' => home_url($l[1])]], [['All tools', '/tools/'], ['Product image studio', '/tools/product-image-tools/'], ['Profit calculator', '/tools/profit-calculator/'], ['Invoice generator', '/tools/invoice-generator/'], ['UTM link builder', '/tools/utm-builder/']]), '__globals__' => ['text_color' => 'globals/colors?id=secondary', 'text_color_hover' => 'globals/colors?id=primary']]) : $text(''),
            ]),
            $text('<p>© ' . gmdate('Y') . ' Seller Toolkit. Business details, contact and policies will be published before launch.</p>', 'secondary', 13),
        ])], [['type' => 'include', 'name' => 'general', 'sub_name' => '', 'sub_id' => '']]],
    ];

    $comp = in_array('container', $types, true) ? 'container' : 'section';
    $check = fn($t) => ['_id' => $id(), 'text' => $t, 'selected_icon' => ['value' => 'fas fa-check', 'library' => 'fa-solid']];
    $faqItems = [['Are my photos uploaded?', 'No. Resize, compress, square, bulk and watermark run on your device. Only the background remover (when available) sends the photo you choose to a removal service.'], ['Do I need an account?', 'Not for free tools. An account will be needed for saved presets and paid plans.']];
    $faq = in_array('accordion', $widgets, true)
        ? $w('accordion', ['tabs' => array_map(fn($q) => ['_id' => $id(), 'tab_title' => $q[0], 'tab_content' => '<p>' . $q[1] . '</p>'], $faqItems), '__globals__' => ['title_color' => 'globals/colors?id=text', 'tab_active_color' => 'globals/colors?id=primary', 'content_color' => 'globals/colors?id=secondary']])
        : (in_array('toggle', $widgets, true) ? $w('toggle', ['tabs' => array_map(fn($q) => ['_id' => $id(), 'tab_title' => $q[0], 'tab_content' => '<p>' . $q[1] . '</p>'], $faqItems)]) : $text(implode('', array_map(fn($q) => '<details><summary>' . $q[0] . '</summary><p>' . $q[1] . '</p></details>', $faqItems))));
    $docs = array_merge($docs, [
        [$comp, 'ST · Tool card', [$c($card + ['width' => ['unit' => 'px', 'size' => 380, 'sizes' => []]], [
            $c(['flex_direction' => 'row', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center'], [$img('icons/resize.svg', 28), $heading('FREE', 'span', 12, null, 'accent')]),
            $heading('Marketplace resizer', 'h3', 18), $text('<p>Fit photos to a marketplace canvas with contain or crop.</p>'), $button('Open tool →', home_url('/tools/image-resizer/'), 'link'),
        ])], null],
        [$comp, 'ST · Pricing card', [$c($card + ['width' => ['unit' => 'px', 'size' => 300, 'sizes' => []]], [
            $heading('Image Pro', 'h3', 18), $heading('Price pending', 'div', 22), $text('<p>In development · renews monthly</p>', 'secondary', 13),
            in_array('icon-list', $widgets, true) ? $w('icon-list', ['icon_list' => [$check('20 saved presets'), $check('5 brand kits'), $check('25 saved project settings')], '__globals__' => ['icon_color' => 'globals/colors?id=accent', 'text_color' => 'globals/colors?id=text']]) : $text('<ul><li>20 saved presets</li></ul>'),
            $button('Not on sale yet', '', 'muted'),
        ])], null],
        [$comp, 'ST · FAQ', [$c(['flex_direction' => 'column', 'content_width' => 'boxed', 'flex_gap' => ['unit' => 'px', 'size' => 16, 'column' => '16', 'row' => '16']], [$heading('Questions', 'h2', 28), $faq])], null],
        [$comp, 'ST · Comparison table', [$c(['flex_direction' => 'column', 'content_width' => 'boxed'], [$heading('Compare features', 'h2', 28), $text('<table style="width:100%;border-collapse:collapse"><thead><tr><th style="text-align:left">Feature</th><th>Free</th><th>Image Pro</th><th>Seller Desk</th><th>Seller Suite</th></tr></thead><tbody><tr><td>Local image tools</td><td>✓</td><td>✓</td><td>✓</td><td>✓</td></tr><tr><td>Saved presets</td><td>—</td><td>20</td><td>—</td><td>20</td></tr><tr><td>Seats</td><td>—</td><td>1</td><td>2</td><td>2</td></tr></tbody></table>', 'text', 14)])], null],
        [$comp, 'ST · Trust note', [$c(['flex_direction' => 'row', 'flex_align_items' => 'center', 'flex_gap' => ['unit' => 'px', 'size' => 12, 'column' => '12', 'row' => '12'], 'padding' => $box(12, 16), 'border_radius' => $box(10), 'background_background' => 'classic', '__globals__' => ['background_color' => 'globals/colors?id=stsurf']], [$img('icons/lock.svg', 20), $text('<p>Photos stay on this device. Output files have new names and no photo metadata.</p>', 'text', 14)])], null],
        [$comp, 'ST · CTA band', [$c(['content_width' => 'boxed', 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => ['unit' => 'px', 'size' => 16, 'column' => '16', 'row' => '16'], 'padding' => $box(32), 'border_radius' => $box(12), 'background_background' => 'classic', '__globals__' => ['background_color' => 'globals/colors?id=sttint']], [
            $c(['flex_direction' => 'column', 'flex_gap' => ['unit' => 'px', 'size' => 6, 'column' => '6', 'row' => '6'], 'width' => ['unit' => '%', 'size' => 60, 'sizes' => []]], [$heading('Prepare your next listing photos', 'h2', 26), $text('<p>Free, in your browser. No signup needed.</p>')]),
            $button('Try free tools', home_url('/tools/')),
        ])], null],
    ]);

    $report = ['widgets_used' => array_values(array_intersect(['nav-menu', 'icon-list', 'accordion', 'toggle'], $widgets)), 'component_type' => $comp];
    $cm = class_exists('\ElementorPro\Modules\ThemeBuilder\Module') ? \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager() : null;
    foreach ($docs as [$type, $title, $elements, $conditions]) {
        $existing = get_posts(['post_type' => 'elementor_library', 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids']);
        if ($existing) { if ($conditions && $cm) { $cm->save_conditions($existing[0], $conditions); } $report[$title] = 'exists #' . $existing[0] . ($conditions ? ' (conditions saved)' : ''); continue; }
        $doc = $E->documents->create($type, ['post_title' => $title, 'post_status' => 'publish']);
        if (!$doc || is_wp_error($doc)) { $report[$title] = 'CREATE FAILED'; continue; }
        $doc->save(['elements' => $elements]);
        if ($conditions && $cm) { $cm->save_conditions($doc->get_main_id(), $conditions); }
        $report[$title] = $type . ' #' . $doc->get_main_id() . ($conditions ? ' (include/general)' : '');
        ST_Audit::log('elementor.template.created', 'elementor_library', (string) $doc->get_main_id(), ['title' => $title, 'type' => $type]);
    }
    $E->files_manager->clear_cache();
    return $report;
})();
