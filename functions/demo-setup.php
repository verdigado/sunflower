<?php
/**
 * Create demo pages, posts, events and menus on first theme activation.
 *
 * @package Sunflower 26
 */

/**
 * Main entry point – called from activation.php after demo images are imported.
 *
 * @param array $image_ids Attachment IDs keyed by filename without extension
 *                         (e.g. $image_ids['Kuppel'] = 42).
 * @param bool  $force     If true, bypass first-install/legacy guards.
 */
function sunflower_create_demo_content( array $image_ids, bool $force = false ) {
	if ( ! $force && get_option( 'sunflower_demo_content_created' ) ) {
		return;
	}

	$image_urls = array();
	foreach ( $image_ids as $name => $id ) {
		$image_urls[ $name ] = wp_get_attachment_url( $id );
	}

	$page_ids = sunflower_create_demo_pages( $image_ids, $image_urls );

	if ( ! empty( $page_ids['startseite'] ) && ! empty( $page_ids['aktuelles'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['startseite'] );
		update_option( 'page_for_posts', $page_ids['aktuelles'] );
	}

	sunflower_create_demo_posts( $image_ids, $image_urls );
	sunflower_create_demo_events( $image_ids );

	sunflower_create_demo_menus( $page_ids );

	update_option( 'sunflower_demo_content_created', true );
}

/**
 * Get ID of existing demo page by slug.
 *
 * @param string $slug The desired post_name (Slug).
 * @return int|false   Post ID or false if not found.
 */
function sunflower_get_demo_page_id_by_slug( string $slug ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	return $page ? (int) $page->ID : false;
}

/**
 * Create the demo pages and return their IDs.
 *
 * @param array $image_ids Attachment IDs keyed by name.
 * @param array $image_urls Attachment URLs keyed by name.
 * @return array Page IDs keyed by slug.
 */
function sunflower_create_demo_pages( array $image_ids, array $image_urls ) {
	$ids = array();
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	/**
	 * Load pattern and replace {{theme_url}} and image placeholders.
	 *
	 * @param string $path Absolute path to the HTML file.
	 * @return string Block content.
	 */
	$load_pattern = static function ( $path ) use ( $uri, $image_ids, $image_urls ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $path );
		if ( false === $content ) {
			return '';
		}
		$content = str_replace( '{{theme_url}}', $uri, $content );
		foreach ( $image_ids as $name => $id ) {
			$content = str_replace( '__IMG_ID_' . $name . '__', (string) $id, $content );
		}
		foreach ( $image_urls as $name => $url ) {
			$content = str_replace( '__IMG_URL_' . $name . '__', esc_url_raw( $url ), $content );
		}
		return $content;
	};

	$pages = array(
		'startseite' => array(
			'title'   => 'Startseite',
			'slug'    => 'startseite',
			'content' => $load_pattern( $dir . '/functions/block-patterns/seiten/startseite.html' ),
			'meta'    => array( '_sunflower_styled_layout' => '1' ),
		),
		'aktuelles'  => array(
			'title'   => 'Aktuelles',
			'slug'    => 'aktuelles',
			'content' => '',
			'meta'    => array(),
		),
		'kandidatin' => array(
			'title'   => 'Kandidatin',
			'slug'    => 'kandidatin',
			'content' => $load_pattern( $dir . '/functions/block-patterns/seiten/kandidierende.html' ),
			'meta'    => array(),
		),
		'kontakt'    => array(
			'title'   => 'Kontakt',
			'slug'    => 'kontakt',
			'content' => '<!-- wp:sunflower/contact-form /-->',
			'meta'    => array(),
		),
	);

	foreach ( $pages as $key => $def ) {

		$existing_id = sunflower_get_demo_page_id_by_slug( $def['slug'] );

		$postarr = array(
			'post_title'     => $def['title'],
			'post_name'      => $def['slug'],
			'post_content'   => $def['content'],
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		if ( $existing_id ) {
			$postarr['ID'] = $existing_id;
			$page_id       = wp_update_post( $postarr, true );
		} else {
			$page_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $page_id ) ) {
			continue;
		}

		foreach ( $def['meta'] as $meta_key => $meta_value ) {
			update_post_meta( $page_id, $meta_key, $meta_value );
		}

		$ids[ $key ] = (int) $page_id;
	}

	return array_filter( $ids );
}

/**
 * Replace image placeholders.
 *
 * @param string $content Raw block content with __IMG_ID_xxx__ / __IMG_URL_xxx__ tokens.
 * @param array  $ids     Attachment IDs.
 * @param array  $urls    Attachment URLs.
 * @return string
 */
function sunflower_replace_image_tokens( $content, array $ids, array $urls ) {
	foreach ( $ids as $name => $id ) {
		$content = str_replace( '__IMG_ID_' . $name . '__', (string) $id, $content );
	}
	foreach ( $urls as $name => $url ) {
		$content = str_replace( '__IMG_URL_' . $name . '__', esc_url_raw( $url ), $content );
	}
	// Clean up any stray double-commas left by removed JSON fields.
	$content = preg_replace( '/,\s*,/', ',', $content );
	return $content;
}

/**
 * Get ID of existing demo post by slug.
 *
 * @param string $slug The desired post_name (Slug).
 * @return int|false   Post ID or false if not found.
 */
function sunflower_get_demo_post_id_by_slug( string $slug ) {
	$post = get_page_by_path( $slug, OBJECT, 'post' );
	return $post ? (int) $post->ID : false;
}

/**
 * Create demo blog posts.
 *
 * @param array $image_ids Attachment IDs keyed by name.
 * @param array $image_urls Attachment URLs keyed by name.
 */
function sunflower_create_demo_posts( array $image_ids, array $image_urls ) {
	$posts = sunflower_get_demo_post_definitions();

	foreach ( $posts as $post_def ) {

		$existing_id = sunflower_get_demo_post_id_by_slug( $post_def['slug'] );

		$postarr = array(
			'ID'             => $existing_id,
			'post_title'     => $post_def['title'],
			'post_name'      => $post_def['slug'],
			'post_content'   => sunflower_replace_image_tokens( $post_def['content'], $image_ids, $image_urls ),
			'post_status'    => 'publish',
			'post_type'      => 'post',
			'post_date'      => gmdate( 'Y-m-d H:i:s', strtotime( $post_def['date_offset'] ) ),
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		if ( $existing_id ) {
			$postarr['ID'] = $existing_id;
			$post_id       = wp_update_post( $postarr, true );
		} else {
			$post_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		if ( $post_id && ! empty( $image_ids[ $post_def['thumbnail'] ] ) ) {
			set_post_thumbnail( $post_id, $image_ids[ $post_def['thumbnail'] ] );
		}
	}
}

/**
 * Post definitions: title, slug, thumbnail key, date offset, block content.
 *
 * @return array[]
 */
function sunflower_get_demo_post_definitions() {
	return array(
		array(
			'title'       => 'Unsere grüne Lunge schützen',
			'slug'        => 'unsere-gruene-lunge-schuetzen',
			'thumbnail'   => 'Wald',
			'date_offset' => '-5 days',
			'content'     => <<<'BLOCK'
<!-- wp:paragraph -->
<p>Unsere Wälder erfüllen vielfältige Funktionen – sie sind Lebensraum für tausende Arten, speichern Kohlenstoff, filtern Wasser und bieten Erholung. Doch Hitze, Trockenheit und Monokulturen setzen ihnen zu. In Baden‑Württemberg sank die durchschnittliche Kronenverlichtung 2025 auf <strong>26,4&nbsp;%</strong>, und der Anteil stark geschädigter Bäume auf 42&nbsp;%. Fichten profitieren von geringerer Borkenkäfer‑Belastung; Tannen leiden unter Mistletoe‑Befall und Douglasien unter Pilzkrankheiten und Gallmücken.</p>
<!-- /wp:paragraph -->

<!-- wp:media-text {"mediaId":__IMG_ID_Kuppel__,"mediaType":"image","mediaPosition":"right"} -->
<div class="wp-block-media-text has-media-on-the-right is-stacked-on-mobile"><div class="wp-block-media-text__content"><!-- wp:paragraph -->
<p>Buchen erholen sich dank feuchter Sommer – ihr Blattverlust sank auf <strong>27,7&nbsp;%</strong> – und auch Eichen zeigten moderate Verbesserungen. In Niedersachsen verschlechterte sich hingegen der Kronenzustand: <strong>23&nbsp;% der Bäume</strong> weisen deutliche Schäden auf, der Anteil stark geschädigter Bäume stieg auf <strong>4,2&nbsp;%</strong>. Dürre und Starkwinde belasten die Wälder und erhöhen die Anfälligkeit gegenüber Schadinsekten.</p>
<!-- /wp:paragraph --></div><figure class="wp-block-media-text__media"><img src="__IMG_URL_Kuppel__" alt="" class="wp-image-__IMG_ID_Kuppel__"/></figure></div>
<!-- /wp:media-text -->

<!-- wp:paragraph -->
<p>Um die Wälder klimaresilient zu machen, fördert Niedersachsen 2026 die Umwandlung von Monokulturen in Mischwälder mit <strong>über 44&nbsp;Millionen&nbsp;Euro</strong>. Bundesweit sind Laubbäume inzwischen <strong>43&nbsp;% der Waldfläche</strong>, doch <strong>64&nbsp;% der Wälder</strong> entsprechen noch nicht der natürlichen Artenzusammensetzung. Wir Grüne wollen naturnahe Bewirtschaftung fördern: Mischwälder aus heimischen Arten, längere Umtriebszeiten, mehr Naturwaldreservate und die Wiedervernässung entwässerter Böden.</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":__IMG_ID_Alpen__,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="__IMG_URL_Alpen__" alt="" class="wp-image-__IMG_ID_Alpen__"/></figure>
<!-- /wp:image -->
BLOCK
			,
		),

		array(
			'title'       => 'Fahrradwege: Raum fürs Rad',
			'slug'        => 'fahrradwege-raum-fuers-rad',
			'thumbnail'   => 'Fahrrad',
			'date_offset' => '-10 days',
			'content'     => <<<'BLOCK'
<!-- wp:paragraph -->
<p>Radfahren entlastet unsere Städte, schützt das Klima und macht Menschen unabhängig vom Auto. Der Bund setzt 2026 Akzente: Über den Klima‑ und Transformationsfonds stehen <strong>552,6 Millionen Euro</strong> für aktive Mobilität bereit, darunter <strong>281 Millionen für das Sonderprogramm „Stadt und Land"</strong>, <strong>58,5 Millionen für Radschnellwege</strong> und <strong>100 Millionen für Radwege entlang von Bundesstraßen</strong>.<br>Zusätzlich unterstützt der Bund Lastenräder mit acht Millionen Euro und investiert in Abstellanlagen an Bahnhöfen.</p>
<!-- /wp:paragraph -->

<!-- wp:gallery {"columns":2,"linkTo":"none"} -->
<figure class="wp-block-gallery has-nested-images columns-2 is-cropped"><!-- wp:image {"id":__IMG_ID_Leuchtturm__,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="__IMG_URL_Leuchtturm__" alt="" class="wp-image-__IMG_ID_Leuchtturm__"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":__IMG_ID_Alpen__,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="__IMG_URL_Alpen__" alt="" class="wp-image-__IMG_ID_Alpen__"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":__IMG_ID_TheSunflower__,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="__IMG_URL_TheSunflower__" alt="" class="wp-image-__IMG_ID_TheSunflower__"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":__IMG_ID_Wald__,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="__IMG_URL_Wald__" alt="" class="wp-image-__IMG_ID_Wald__"/></figure>
<!-- /wp:image --></figure>
<!-- /wp:gallery -->

<!-- wp:paragraph -->
<p>Baden‑Württemberg zeigt, wie ambitionierte Ziele aussehen können: Bis 2030 sollen <strong>1 400 Kilometer neue Radwege</strong> und <strong>30 000 zusätzliche Fahrradparkplätze</strong> entstehen; über <strong>1 000 Projekte</strong> mit Investitionen von mehr als einer Milliarde Euro sind geplant. Wir Grüne setzen uns für durchgehende, sichere Netze, großflächige Tempo‑30‑Zonen und ein kinderfreundliches Verkehrsklima ein.</p>
<!-- /wp:paragraph -->
BLOCK
			,
		),

		array(
			'title'       => 'Mehr Züge, weniger Emissionen!',
			'slug'        => 'mehr-zuege-weniger-emissionen',
			'thumbnail'   => 'ICE',
			'date_offset' => '-15 days',
			'content'     => <<<'BLOCK'
<!-- wp:paragraph -->
<p>Investitionen in die Schiene sind die Grundlage für klimafreundliche Mobilität und wirtschaftliche Stärke. Die Deutsche Bahn und der Bund planen für 2026 Rekordausgaben von über <strong>23&nbsp;Milliarden&nbsp;Euro</strong>, mehr als je zuvor, um das Netz zu modernisieren, Stationen barrierefrei auszubauen und die Digitalisierung voranzubringen.</p>
<!-- /wp:paragraph -->

<!-- wp:media-text {"mediaId":__IMG_ID_Biene__,"mediaType":"image"} -->
<div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="__IMG_URL_Biene__" alt="" class="wp-image-__IMG_ID_Biene__"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph -->
<p>Mehr als die Hälfte der Investitionen dient der Erhaltung des bestehenden Netzes; der Rest geht in digitale Stellwerke, neue Strecken und die Modernisierung von Bahnhöfen. Wichtige Projekte wie die Generalsanierung der Strecke Hamburg–Berlin sollen Engpässe beseitigen.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->

<!-- wp:paragraph -->
<p>Wir Grüne fordern ein flächendeckendes Deutschland‑Takt‑System mit verlässlichen Umsteigezeiten, mehr Güterverkehr auf der Schiene und die lückenlose Elektrifizierung auch ländlicher Strecken. Investitionen in Nacht- und Fernverkehr sowie grenzüberschreitende Verbindungen sind zentrale Bausteine einer echten Verkehrswende.</p>
<!-- /wp:paragraph -->
BLOCK
			,
		),

		array(
			'title'       => 'Bienensterben und Bienenschutz',
			'slug'        => 'bienensterben-und-bienenschutz',
			'thumbnail'   => 'Biene',
			'date_offset' => '-20 days',
			'content'     => <<<'BLOCK'
<!-- wp:paragraph -->
<p>Mehr als 560 Wildbienenarten gibt es in Deutschland; davon gelten nur <strong>37 % als nicht bedroht</strong>. Der massive Einsatz von Pestiziden – etwa <strong>30 000 Tonnen</strong> pro Jahr – zerstört Lebensräume und schädigt das Nervensystem der Bienen. Herbizide wie Glyphosat beseitigen Blühpflanzen und entziehen den Insekten Nahrung.</p>
<!-- /wp:paragraph -->

<!-- wp:media-text {"mediaId":__IMG_ID_Biene__,"mediaType":"image"} -->
<div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="__IMG_URL_Biene__" alt="" class="wp-image-__IMG_ID_Biene__"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph -->
<p>Obwohl Honigbienen gut betreut werden, sind gerade die Wildbienen für die Bestäubung unverzichtbar: <strong>84&nbsp;% der wichtigsten Nutzpflanzen in Europa</strong> werden von Bienen und anderen Insekten bestäubt; dieser Service ist <strong>22&nbsp;Milliarden&nbsp;Euro</strong> pro Jahr wert.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->

<!-- wp:paragraph -->
<p>Um das Bienensterben zu stoppen, wollen wir Blühstreifen, Streuobstwiesen, Hecken und Feuchtgebiete fördern und Flächenversiegelung einschränken. Auf EU-Ebene setzen wir uns für eine Agrarreform ein, die Artenvielfalt honoriert und Monokulturen zurückdrängt.</p>
<!-- /wp:paragraph -->
BLOCK
			,
		),

		array(
			'title'       => 'Watt: Zwischen Land und Meer',
			'slug'        => 'watt-zwischen-land-und-meer',
			'thumbnail'   => 'Leuchtturm',
			'date_offset' => '-25 days',
			'content'     => <<<'BLOCK'
<!-- wp:paragraph -->
<p>Das Wattenmeer an der Nordseeküste der Niederlande, Deutschlands und Dänemarks ist das größte zusammenhängende Wattgebiet der Welt. Es umfasst etwa <strong>12&nbsp;000&nbsp;Quadratkilometer</strong> und wurde 2009 als UNESCO‑Weltnaturerbe anerkannt. Jedes Jahr rasten hier <strong>10–12&nbsp;Millionen Zug- und Watvögel</strong>.</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":__IMG_ID_Duenen__,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="__IMG_URL_Duenen__" alt="" class="wp-image-__IMG_ID_Duenen__"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Doch der Druck wächst. Der Klimawandel lässt den Meeresspiegel steigen und gefährdet Brutplätze. Wir Grüne setzen uns für ein integriertes Küstenzonenmanagement ein: bessere Kläranlagen, Reduktion von Nährstoffeinträgen aus der Landwirtschaft, strenge Regeln für Offshore‑Windparks und nachhaltigen Tourismus.</p>
<!-- /wp:paragraph -->
BLOCK
			,
		),

		array(
			'title'       => 'Starthilfe für den Elektroantrieb',
			'slug'        => 'starthilfe-fuer-den-elektroantrieb',
			'thumbnail'   => 'Elektroauto',
			'date_offset' => '-30 days',
			'content'     => <<<'BLOCK'
<!-- wp:paragraph -->
<p>Die Elektromobilität gewinnt an Fahrt. Im Februar&nbsp;2026 wurden in Deutschland <strong>46 275 neue Elektroautos</strong> zugelassen – das waren 28,7&nbsp;% mehr als im Vorjahresmonat. Ihr Marktanteil lag bei <strong>21,9&nbsp;%</strong>, nahe am Rekordwert von 22&nbsp;%.</p>
<!-- /wp:paragraph -->

<!-- wp:media-text {"mediaId":__IMG_ID_Elektroauto__,"mediaType":"image"} -->
<div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="__IMG_URL_Elektroauto__" alt="" class="wp-image-__IMG_ID_Elektroauto__"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph -->
<p>Die Bundesregierung möchte diesen Trend mit einem neuen Förderprogramm verstärken. Förderfähig sind Fahrzeuge, die ab dem <strong>1.&nbsp;Januar&nbsp;2026</strong> neu zugelassen werden. Die Förderung richtet sich an Privatpersonen und ist sozial gestaffelt.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->

<!-- wp:paragraph -->
<p>Trotz steigender Verkaufszahlen besitzen bislang nur <strong>6 % der deutschen Haushalte</strong> ein Elektroauto oder Plug‑in‑Hybrid. Um den Hochlauf zu erleichtern, braucht es eine flächendeckende Ladeinfrastruktur: Zum 1. Januar 2025 gab es <strong>160 000 öffentliche Ladepunkte</strong>, davon <strong>36 000 Schnelllader</strong>.</p>
<!-- /wp:paragraph -->
BLOCK
			,
		),
	);
}

/**
 * Get ID of existing demo event by slug.
 *
 * @param string $slug The slug of the event to find.
 * @return int|false   Post ID or false.
 */
function sunflower_get_demo_event_id_by_slug( string $slug ) {
	$post = get_page_by_path( $slug, OBJECT, 'sunflower_event' );
	return $post ? (int) $post->ID : false;
}

/**
 * Create demo events.
 *
 * @param array $image_ids Attachment IDs keyed by name.
 */
function sunflower_create_demo_events( array $image_ids ) {
	$lorem = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.';

	$content = '<!-- wp:paragraph --><p>' . $lorem . '</p><!-- /wp:paragraph -->';

	$events = array(
		array(
			'title'      => 'Beispieltermin 1',
			'slug'       => 'beispieltermin-1',
			'thumbnail'  => 'TheSunflower',
			'from_days'  => 365 + 7,
			'until_days' => 365 + 7,
			'location'   => 'Rathaus Musterstadt, Rathausplatz 1',
		),
		array(
			'title'      => 'Beispieltermin 2',
			'slug'       => 'beispieltermin-2',
			'thumbnail'  => 'Fahrrad',
			'from_days'  => 365 + 14,
			'until_days' => 14,
			'location'   => 'Stadtbibliothek Musterstadt',
		),
		array(
			'title'      => 'Beispieltermin 3',
			'slug'       => 'beispieltermin-3',
			'thumbnail'  => 'ICE',
			'from_days'  => 365 + 21,
			'until_days' => 365 + 21,
			'location'   => 'Bürgerhaus Musterstadt',
		),
		array(
			'title'      => 'Beispieltermin 4',
			'slug'       => 'beispieltermin-4',
			'thumbnail'  => 'Duenen',
			'from_days'  => 365 + 28,
			'until_days' => 365 + 28,
			'location'   => 'Marktplatz Musterstadt',
		),
		array(
			'title'      => 'Beispieltermin 5',
			'slug'       => 'beispieltermin-5',
			'thumbnail'  => 'Wald',
			'from_days'  => 365 + 35,
			'until_days' => 365 + 35,
			'location'   => 'Kulturzentrum Musterstadt',
		),
		array(
			'title'      => 'Beispieltermin 6',
			'slug'       => 'beispieltermin-6',
			'thumbnail'  => 'Alpen',
			'from_days'  => 365 + 42,
			'until_days' => 365 + 42,
			'location'   => 'Online-Veranstaltung',
		),
	);

	foreach ( $events as $ev ) {
		$from  = gmdate( 'Y-m-d H:i:s', strtotime( '+' . $ev['from_days'] . ' days 18:00:00' ) );
		$until = gmdate( 'Y-m-d H:i:s', strtotime( '+' . $ev['until_days'] . ' days 20:00:00' ) );

		$existing_id = sunflower_get_demo_event_id_by_slug( $ev['slug'] );

		$postarr = array(
			'post_title'     => $ev['title'],
			'post_name'      => $ev['slug'],
			'post_content'   => $content,
			'post_status'    => 'publish',
			'post_type'      => 'sunflower_event',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		if ( $existing_id ) {
			$postarr['ID'] = $existing_id;
			$event_id      = wp_update_post( $postarr, true );
		} else {
			$event_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $event_id ) ) {
			continue;
		}

		update_post_meta( $event_id, '_sunflower_event_from', $from );
		update_post_meta( $event_id, '_sunflower_event_until', $until );
		update_post_meta( $event_id, '_sunflower_event_location', $ev['location'] );

		if ( ! empty( $image_ids[ $ev['thumbnail'] ] ) ) {
			set_post_thumbnail( $event_id, $image_ids[ $ev['thumbnail'] ] );
		}
	}
}

/**
 * Create header and footer navigation menus.
 *
 * @param array $page_ids Page IDs keyed by slug.
 */
function sunflower_create_demo_menus( array $page_ids ) {
	// Header menu: Kandidatin, Aktuelles, Kontakt.
	$header_menu_id = wp_create_nav_menu( 'Hauptmenü' );
	if ( ! is_wp_error( $header_menu_id ) ) {
		$header_items = array(
			array(
				'id'    => $page_ids['kandidatin'] ?? 0,
				'label' => 'Kandidatin',
			),
			array(
				'id'    => $page_ids['aktuelles'] ?? 0,
				'label' => 'Aktuelles',
			),
			array(
				'id'    => $page_ids['kontakt'] ?? 0,
				'label' => 'Kontakt',
			),
		);
		foreach ( $header_items as $item ) {
			if ( empty( $item['id'] ) ) {
				continue;
			}
			wp_update_nav_menu_item(
				$header_menu_id,
				0,
				array(
					'menu-item-object-id' => $item['id'],
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-title'     => $item['label'],
					'menu-item-status'    => 'publish',
				)
			);
		}
	}

	$footer_menu_id = wp_create_nav_menu( 'Footer (Rechtliches)' );
	if ( ! is_wp_error( $footer_menu_id ) ) {
		$footer_links = array(
			array(
				'label' => 'Impressum',
				'url'   => home_url( '/impressum' ),
			),
			array(
				'label' => 'Datenschutz',
				'url'   => home_url( '/datenschutz' ),
			),
		);
		foreach ( $footer_links as $link ) {
			wp_update_nav_menu_item(
				$footer_menu_id,
				0,
				array(
					'menu-item-type'   => 'custom',
					'menu-item-url'    => $link['url'],
					'menu-item-title'  => $link['label'],
					'menu-item-status' => 'publish',
				)
			);
		}
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_wp_error( $header_menu_id ) ) {
		$locations['mainmenu'] = $header_menu_id;
	}
	if ( ! is_wp_error( $footer_menu_id ) ) {
		$locations['footer1'] = $footer_menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}
