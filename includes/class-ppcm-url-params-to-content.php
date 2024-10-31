<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PPCM_URL_Params_To_Content
 */
class PPCM_URL_Params_To_Content {
	
    private $is_aio = false;
    private $is_yoast = false;
    
	/**
	 * Constructor.
	 */
	public function __construct() {
	 
		/* The following requires All In One SEO Pack plugin or Yoast, so don't load these actions if plugin not installed */
		if ( ( $this->is_aio = defined( 'AIOSEO_VERSION' ) )
             || ( $this->is_yoast = defined( 'WPSEO_VERSION' ) ) ) {
		    
		    /* If in an admin page, init meta box */
			if ( is_admin() ) {
				add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
				add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
			}
			
			/* All-In-One SEO Pack */
			if ( $this->is_aio ) {
				if ( version_compare( AIOSEO_VERSION, '4.0.0', '<' ) ) {
                    add_filter( 'aioseop_description', array( $this, 'change_seo_meta_desc_for_url_params' ), PHP_INT_MAX, 1 );
                    add_filter( 'aioseop_title', array( $this, 'change_seo_page_title_for_url_params' ), PHP_INT_MAX, 1 );
                } else {
                    add_filter( 'aioseo_description', array( $this, 'change_seo_meta_desc_for_url_params' ), PHP_INT_MAX, 1 );
                    add_filter( 'aioseo_title', array( $this, 'change_seo_page_title_for_url_params' ), PHP_INT_MAX, 1 );
                }
			};
			
			/* Yoast SEO */
			if ( $this->is_yoast ) {
                add_filter( 'wpseo_metadesc', array( $this, 'change_seo_meta_desc_for_url_params' ), PHP_INT_MAX, 1 );
                add_filter( 'wpseo_title', array( $this, 'change_seo_page_title_for_url_params' ), PHP_INT_MAX, 1 );
            }
		}
		
		/* Register shortcodes */
		/** URL Params to text shortcode @see PPCM_URL_Params_To_Content::shortcode_url_params_to_text() */
		add_shortcode( 'url_params_to_text', array( $this, 'shortcode_url_params_to_text' ) );
	}
	
	/**
	 * URL params meta box initialization.
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
		add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
	}
	
	/**
	 * Adds the URL params meta box.
	 */
	public function add_metabox() {
		add_meta_box(
			'ppcm-url-param-meta-box',
			__( 'PPC Masterminds Meta Settings', 'ppc-masterminds' ),
			array( $this, 'render_metabox' ),
			array('page', 'post'),
			'advanced',
			'default'
		);
		
	}
	
	/**
	 * Renders the URL params meta box.
	 */
	public function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'ppcm_url_params_save', 'ppcm_url_params_nonce' );
		
		$params_list = get_post_meta( $post->ID, 'ppcm_url_params_list', true);
		$params_title = get_post_meta( $post->ID, 'ppcm_url_params_title', true);
		$params_description = get_post_meta( $post->ID, 'ppcm_url_params_meta_description', true);
		
		?>
		<p>When a url param matches the params listed below, it'll swap any {param} text in the title or meta description fields below with that url parameter. If there is no match,
			or if the page title or page meta description fields below are empty, the page will use the <strong><?php esc_html_e( $this->is_aio ? 'All In One SEO Pack' : 'Yoast SEO' );?></strong> title or description instead.</p>
		<p>For example, for https://mysite.com/?my_param=Foo, if the param was my_param, then "{param}" would be replaced with "Foo" wherever it exists in the title and meta description.</p>
		<p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label for="ppcm_url_params_list" class="post-attributes-label">
				<span style="font-weight:600;"><?php esc_attr_e( 'List of URL Params', 'ppc-masterminds' ); ?></span><br>
			</label>
		</p>
		<input type="text" value="<?php echo ( $params_list ? $params_list : '' ); ?>" name="ppcm_url_params_list" id="ppcm_url_params_list" style="width:100%;" /><br>
		<span class="description"><?php esc_attr_e( 'Case sensitive. Comma delimited (ie my_param,my_other_param). Only the first matching param is used.', 'ppc-masterminds' ); ?></span><br>
		
		<p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label for="ppcm_url_params_title" class="post-attributes-label">
				<span style="font-weight:600;"><?php esc_attr_e( 'Page Title', 'ppc-masterminds' ); ?></span><br>
			</label>
		</p>
		<input type="text" value="<?php echo ( $params_title ? $params_title : '' ); ?>" name="ppcm_url_params_title" id="ppcm_url_params_title" style="width:100%;" maxlength="60" /><br>
		<span class="description"><?php esc_attr_e( 'This page title is used when url params match. Any "{param}" in the text gets replaced by the url param content.', 'ppc-masterminds' ); ?></span><br>
		
		<p class="post-attributes-label-wrapper page-template-label-wrapper">
			<label for="ppcm_url_params_meta_description" class="post-attributes-label">
				<span style="font-weight:600;"><?php esc_attr_e( 'Page Meta Description', 'ppc-masterminds' ); ?></span><br>
			</label>
		</p>
		<textarea style="width:100%;" id="ppcm_url_params_meta_description" name="ppcm_url_params_meta_description" placeholder="" maxlength="160"><?php echo ( $params_description ? $params_description : '' ); ?></textarea><br>
		<span class="description"><?php esc_attr_e( 'This meta description is used when url params match. Any "{param}" in the text gets replaced by the url param content.', 'ppc-masterminds' ); ?></span><br>
		
		<?php
		
	}
	
	/**
	 * Handles saving the URL params meta box data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {
		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['ppcm_url_params_nonce'] ) ? $_POST['ppcm_url_params_nonce'] : '';
		$nonce_action = 'ppcm_url_params_save';
		
		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}
		
		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}
		
		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		// Save data
		if ( isset( $_POST['ppcm_url_params_list'] ) ) {
			update_post_meta( $post_id, 'ppcm_url_params_list', sanitize_text_field( $_POST['ppcm_url_params_list'] ) );
		}
		if ( isset( $_POST['ppcm_url_params_title'] ) ) {
			update_post_meta( $post_id, 'ppcm_url_params_title', sanitize_text_field( $_POST['ppcm_url_params_title'] ) );
		}
		if ( isset( $_POST['ppcm_url_params_meta_description'] ) ) {
			update_post_meta( $post_id, 'ppcm_url_params_meta_description', sanitize_text_field( $_POST['ppcm_url_params_meta_description'] ) );
		}
		
		return;
	}
	
	/**
     * Shortcode to insert url param in text in page or title
     *
     * @param $atts
     *
     * @return string
     */
    public function shortcode_url_params_to_text( $atts ) {
        
        $atts = shortcode_atts( array(
            'params'  => '',
            'default' => '',
            'text'    => '',
        ), $atts, 'url_param' );
        
        // Param is the only required attribute
        if ( empty( $atts['params'] ) ) {
            return '';
        }
        
        // Extract comma delimited params
        $params = explode( ',', $atts['params'] );
        
        // Set result to default first
        $result = $atts['default'];
        
        // Cycle through each possible param
        foreach ( $params as $param ) {
            
            $param = trim( $param );
            
            // If the url parameter exists and is not blank
            if ( ! empty( $_GET[ $param ] ) ) {
                $result = htmlspecialchars( $_GET[ $param ], ENT_QUOTES );
                
                // If there is a text template to use, use it
                if ( ! empty( $atts['text'] ) ) {
                    $result = str_replace( '{param}', $result, $atts['text'] );
                }
                
                // Use only this param since it matches
                break;
            }
        }
        
        return $result;
    }
	
	
	/**
	 * Change meta description if url params are set
	 *
	 * @param $description
	 *
	 * @return string|string[]
	 */
	public function change_seo_meta_desc_for_url_params( $description ) {
		
		global $wp_query, $post;
		
		if ( ! is_object( $post ) ) {
			if ( ! $wp_query ) {
				return $description;
			}
			$post = $wp_query->get_queried_object();
		}
		
		$params_description = get_post_meta( $post->ID, 'ppcm_url_params_meta_description', true );
		
		// Don't do anything if there is no {param} text to replace
		if ( strpos( $params_description, '{param}' ) === false ) {
			return $description;
		}
		
		$params_list = get_post_meta( $post->ID, 'ppcm_url_params_list', true );
		
		// If no params list was set, return the default text
		if ( empty( $params_list ) ) {
			return $description;
		}
		
		$params_list = explode( ',', $params_list );
		
		foreach ( $params_list as $param ) {
			
			$param = trim( $param );
			
			if ( ! empty ( $_GET[ $param ] ) ) {
				
				$keyword = htmlspecialchars( $_GET[ $param ], ENT_QUOTES );
				
				return str_replace( '{param}', $keyword, $params_description );
			}
			
		}
		
		return $description;
	}
	
	/**
	 * Change title tag content if url params are set
	 *
	 * @param $title
	 *
	 * @return string|string[]
	 */
	public function change_seo_page_title_for_url_params( $title ) {
		
		global $wp_query, $post;
		
		if ( ! is_object( $post ) ) {
			if ( ! $wp_query ) {
				return $title;
			}
			$post = $wp_query->get_queried_object();
		}
		
		$params_title = get_post_meta( $post->ID, 'ppcm_url_params_title', true );
		
		// Don't do anything if there is no {param} text to replace
		if ( strpos( $params_title, '{param}' ) === false ) {
			return $title;
		}
		
		$params_list = get_post_meta( $post->ID, 'ppcm_url_params_list', true );
		
		// If no params list was set, return the default text
		if ( empty( $params_list ) ) {
			return $title;
		}
		
		$params_list = explode( ',', strtolower( $params_list ) );
		
		foreach ( $params_list as $param ) {
			
			$param = trim( $param );
			
			if ( ! empty ( $_GET[ $param ] ) ) {
				
				$keyword = htmlspecialchars( $_GET[ $param ], ENT_QUOTES );
				
				return str_replace( '{param}', $keyword, $params_title );
			}
			
		}
		
		return $title;
	}
}

new PPCM_URL_Params_To_Content();
