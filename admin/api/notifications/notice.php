<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Admin-Notices class.
 *
 * Creates an admin notice with consistent styling.
 *
 * @package   WPTRT/admin-notices
 * @author    WPTRT <themes@wordpress.org>
 * @copyright 2019 WPTRT
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/WPTRT/admin-notices
 */

namespace SEOKEYWPTRT\AdminNotices;

/**
 * The Admin_Notice class, responsible for creating admin notices.
 *
 * Each notice is a new instance of the object.
 *
 * @since 1.0.0
 */
class Notice {

	/**
	 * The notice-ID.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $id;

	/**
	 * The notice message.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $message;

	/**
	 * The notice title.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $title;

	/**
	 * An instance of the \WPTRT\AdminNotices\Dismiss object.
	 *
	 * @access public
	 * @since 1.0
	 * @var \SEOKEYWPTRT\AdminNotices\Dismiss
	 */
	public $dismiss;

	/**
	 * The notice arguments.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $options = [
		'scope'             => 'global',
		'type'              => 'info',
		'alt_style'         => false,
		'capability'        => 'edit_theme_options',
		'option_prefix'     => 'seokey_option_notice_dismissed',
        'option_include'    => '',
		'screens'           => [],
        'screens_exclude'   => [],
	];

	/**
	 * Allowed HTML in the message.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $allowed_html = [
		'p'         => [],
		'h3'         => [],
		'a'         => [
			'href'  => [],
			'rel'   => [],
			'class' => [],
		],
		'em'        => [],
		'strong'    => [],
		'br'        => [],
		'span'      => [
			'class' => [],
		],
		'div'      => [
			'class' => [],
		],
	];

	/**
	 * An array of allowed types.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $allowed_types = [
		'info',
		'success',
		'error',
		'warning',
	];

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $id      A unique ID for this notice. Can contain lowercase characters and underscores.
	 * @param string $title   The title for our notice.
	 * @param string $message The message for our notice.
	 * @param array  $options An array of additional options to change the defaults for this notice.
	 *                        [
	 *                            'screens'       => (array)  An array of screens where the notice will be displayed.
	 *                                                        Leave empty to always show.
	 *                                                        Defaults to an empty array.
	 *                            'scope'         => (string) Can be "global" or "user".
	 *                                                        Determines if the dismissed status will be saved as an option or user-meta.
	 *                                                        Defaults to "global".
	 *                            'type'          => (string) Can be one of "info", "success", "warning", "error".
	 *                                                        Defaults to "info".
	 *                            'alt_style'     => (bool)   Whether we want to use alt styles or not.
	 *                                                        Defaults to false.
	 *                            'capability'    => (string) The user capability required to see the notice.
	 *                                                        Defaults to "edit_theme_options".
	 *                            'option_prefix' => (string) The prefix that will be used to build the option (or post-meta) name.
	 *                                                        Can contain lowercase latin letters and underscores.
	 *                        ].
	 */
	public function __construct( $id, $title, $message, $options = [] ) {
		// Set the object properties.
		$this->id      = $id;
		$this->title   = $title;
		$this->message = $message;
		$this->options = wp_parse_args( $options, $this->options );
		// Sanity check: Early exit if ID or message are empty.
		if ( ! $this->id || ! $this->message ) {
			return;
		}
		/**
		 * Allow filtering the allowed HTML tags array.
		 *
		 * @since 1.0.2
		 * @param array $allowed_html The list of allowed HTML tags.
		 * @return array
		 */
		$this->allowed_html = apply_filters( 'seokey_admin_notices_allowed_html', $this->allowed_html );
		// Instantiate the Dismiss object.
		$this->dismiss = new Dismiss( $this->id, $this->options['option_prefix'], $this->options['scope'] );
	}

	/**
	 * Prints the notice.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function the_notice() {
		
		// Early exit if we don't want to show this notice.
		if ( ! $this->show() ) {
			return;
		}
		
		$html    = $this->get_title();
		$html    .= $this->get_message();
		$classes = $this->get_classes();
		
		if ( !empty($this->options['state'] ) &&  'permanent' === $this->options['state'] ) {
			$classes = explode(' ', $classes );
			if ( ( $key = array_search( 'is-dismissible', $classes ) ) !== false ) {
				unset($classes[$key]);
			}
			$classes = implode( ' ', $classes );
		}
		$dismiss = '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
		// Print the notice.
		printf(
			'<div id="%1$s" class="%2$s"><span class="notice-icon notice-' . sanitize_html_class( $this->options['type'] ) . '"></span><span class="notice-content">%3$s</span>' . $dismiss . '</div>',
			esc_attr( $this->id ), // The ID.
			esc_attr( $classes ), // The classes.
			$html // The HTML.
		);
	}

	/**
	 * Determine if the notice should be shown or not.
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function show() {

		// Don't show if the user doesn't have the required capability.
		if ( ! current_user_can( $this->options['capability'] ) ) {
			return false;
		}

		// Don't show if we're not on the right screen.
		if ( ! $this->is_screen() ) {
			return false;
		}

		// Do not display notice if specific option value is found
        $option_exclude = $this->options['option_include'];
		if  ( !empty ( $option_exclude ) ) {
            foreach ($option_exclude as $option => $option_value) {
                $abort = get_option($option);
                if ( $option_value !== $abort ) {
                    return false;
                }
            }
        }

		// Don't show if notice has been dismissed.
		if ( $this->dismiss->is_dismissed() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the notice classes.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_classes() {
		$classes = [
			'seokey-notice',
			'is-dismissible',
		];
		// custom classes
		if ( !empty( $this->options['class']) ) {
			foreach ( $this->options['class'] as $class ) {
				$classes[] = sanitize_html_class( $class );
			}
		}
		// Make sure the defined type is allowed.
		$this->options['type'] = in_array( $this->options['type'], $this->allowed_types, true ) ? $this->options['type'] : 'info';

		// Add the class for notice-type.
		$classes[] = 'notice-' . $this->options['type'];

		// Do we want alt styles?
		if ( $this->options['alt_style'] ) {
			$classes[] = 'notice-alt';
		}

		// Combine classes to a string.
		return implode( ' ', $classes );
	}

	/**
	 * Returns the title.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_title() {

		// Sanity check: Early exit if no title is defined.
		if ( ! $this->title ) {
			return '';
		}

		return sprintf(
			'<h2 class="notice-title">%s</h2>',
			wp_strip_all_tags( $this->title )
		);
	}

	/**
	 * Returns the message.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_message() {
		return wp_kses( $this->message, $this->allowed_html );
	}

	/**
	 * Evaluate if we're on the right place depending on the "screens" argument.
	 *
	 * @access private
	 * @since 1.0
	 * @return bool
	 */
	private function is_screen() {
        // Screen exclusion
        if ( ! $this->options['screens_exclude'] || !empty( $this->options['screens_exclude'] ) ) {
            /** @var \WP_Screen $current_screen */
            $current_screen = seokey_helper_get_current_screen();
            // Check if we're on one of the defined excluded screens.
            $good_to_go = in_array( $current_screen->id, $this->options['screens_exclude'], true );
            if ( true === $good_to_go ) {
              return false;
            }
        }

        // If screen is empty we want this shown on all screens.
        if ( ! $this->options['screens'] || empty( $this->options['screens'] ) ) {
			return true;
		}

		/** @var \WP_Screen $current_screen */
		$current_screen = seokey_helper_get_current_screen();
		// Check if we're on one of the defined screens.
        $good_to_go = in_array( $current_screen->id, $this->options['screens'], true );
		return $good_to_go;
	}
}
