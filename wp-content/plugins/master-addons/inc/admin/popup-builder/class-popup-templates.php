<?php
namespace MasterAddons\Inc\Admin\PopupBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Templates {
    
    private $templates = [];
    
    public function __construct() {
        $this->init_templates();
    }
    
    private function init_templates() {
        $this->templates = [
            'newsletter' => [
                'name' => __('Newsletter Signup', 'master-addons'),
                'description' => __('Collect email addresses with a clean newsletter form', 'master-addons'),
                'type' => 'modal',
                'category' => 'lead-generation',
                'content' => '<div class="ma-popup-template-newsletter">
                    <h2>Join Our Newsletter</h2>
                    <p>Get the latest updates and exclusive offers delivered to your inbox.</p>
                    <form class="ma-newsletter-form">
                        <input type="email" placeholder="Enter your email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                    <p class="ma-privacy-text">We respect your privacy. Unsubscribe at any time.</p>
                </div>',
                'settings' => [
                    'trigger_scroll_percent' => 50,
                    'popup_width' => '500px',
                    'popup_animation' => 'fade'
                ]
            ],
            'discount' => [
                'name' => __('Discount Offer', 'master-addons'),
                'description' => __('Promote special offers and discounts', 'master-addons'),
                'type' => 'modal',
                'category' => 'sales',
                'content' => '<div class="ma-popup-template-discount">
                    <div class="ma-discount-badge">SPECIAL OFFER</div>
                    <h2>Get 25% OFF</h2>
                    <p>Limited time offer! Use code <strong>SAVE25</strong> at checkout.</p>
                    <button class="ma-cta-button">Shop Now</button>
                    <p class="ma-expires">Offer expires in 48 hours</p>
                </div>',
                'settings' => [
                    'popup_width' => '450px',
                    'popup_animation' => 'zoom'
                ]
            ],
            'cookie-consent' => [
                'name' => __('Cookie Consent', 'master-addons'),
                'description' => __('GDPR compliant cookie consent notice', 'master-addons'),
                'type' => 'notification',
                'category' => 'compliance',
                'content' => '<div class="ma-popup-template-cookie">
                    <p>We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies. 
                    <a href="/privacy-policy">Learn more</a></p>
                    <button class="ma-accept-cookies">Accept</button>
                    <button class="ma-decline-cookies">Decline</button>
                </div>',
                'settings' => [
                    'popup_position' => 'bottom-center',
                    'popup_width' => '100%',
                    'show_overlay' => false,
                    'close_on_overlay' => false
                ]
            ],
            'contact' => [
                'name' => __('Contact Form', 'master-addons'),
                'description' => __('Quick contact form for visitor inquiries', 'master-addons'),
                'type' => 'slide-in',
                'category' => 'lead-generation',
                'content' => '<div class="ma-popup-template-contact">
                    <h3>Get in Touch</h3>
                    <form class="ma-contact-form">
                        <input type="text" placeholder="Your Name" required>
                        <input type="email" placeholder="Your Email" required>
                        <textarea placeholder="Your Message" rows="4" required></textarea>
                        <button type="submit">Send Message</button>
                    </form>
                </div>',
                'settings' => [
                    'trigger_scroll_percent' => 75,
                    'popup_position' => 'bottom-right',
                    'popup_width' => '350px',
                    'popup_animation' => 'slide-left'
                ]
            ],
            'announcement' => [
                'name' => __('Announcement Bar', 'master-addons'),
                'description' => __('Important announcements and notices', 'master-addons'),
                'type' => 'notification',
                'category' => 'informational',
                'content' => '<div class="ma-popup-template-announcement">
                    <p><strong>Important:</strong> Our store will be closed for maintenance on Sunday. 
                    <a href="#">Learn more →</a></p>
                </div>',
                'settings' => [
                    'popup_position' => 'top-center',
                    'popup_width' => '100%',
                    'show_overlay' => false,
                    'popup_animation' => 'slide-down'
                ]
            ],
            'exit-intent' => [
                'name' => __('Exit Intent', 'master-addons'),
                'description' => __('Capture leaving visitors with special offer', 'master-addons'),
                'type' => 'modal',
                'category' => 'lead-generation',
                'content' => '<div class="ma-popup-template-exit">
                    <h2>Wait! Don\'t Leave Yet</h2>
                    <p>Get an exclusive 15% discount before you go!</p>
                    <form class="ma-exit-form">
                        <input type="email" placeholder="Enter your email for the discount" required>
                        <button type="submit">Get My Discount</button>
                    </form>
                    <p class="ma-no-thanks"><a href="#">No thanks, I\'ll pay full price</a></p>
                </div>',
                'settings' => [
                    'popup_width' => '500px',
                    'popup_animation' => 'zoom',
                    'show_frequency' => 'once_session'
                ]
            ],
            'video' => [
                'name' => __('Video Popup', 'master-addons'),
                'description' => __('Showcase videos in a popup', 'master-addons'),
                'type' => 'modal',
                'category' => 'media',
                'content' => '<div class="ma-popup-template-video">
                    <div class="ma-video-wrapper">
                        <iframe width="560" height="315" 
                            src="https://www.youtube.com/embed/dQw4w9WgXcQ" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>',
                'settings' => [
                    'popup_width' => '800px',
                    'popup_animation' => 'fade'
                ]
            ],
            'age-verification' => [
                'name' => __('Age Verification', 'master-addons'),
                'description' => __('Verify visitor age before accessing content', 'master-addons'),
                'type' => 'full-screen',
                'category' => 'compliance',
                'content' => '<div class="ma-popup-template-age">
                    <h2>Age Verification Required</h2>
                    <p>You must be 18 years or older to enter this site.</p>
                    <div class="ma-age-buttons">
                        <button class="ma-age-confirm">I am 18 or older</button>
                        <button class="ma-age-deny">I am under 18</button>
                    </div>
                </div>',
                'settings' => [
                    'show_close_button' => false,
                    'close_on_overlay' => false,
                    'close_on_esc' => false,
                    'show_frequency' => 'once'
                ]
            ],
            'social-follow' => [
                'name' => __('Social Media Follow', 'master-addons'),
                'description' => __('Encourage social media follows', 'master-addons'),
                'type' => 'corner',
                'category' => 'social',
                'content' => '<div class="ma-popup-template-social">
                    <h4>Follow Us</h4>
                    <div class="ma-social-icons">
                        <a href="#" class="ma-social-facebook">Facebook</a>
                        <a href="#" class="ma-social-twitter">Twitter</a>
                        <a href="#" class="ma-social-instagram">Instagram</a>
                        <a href="#" class="ma-social-youtube">YouTube</a>
                    </div>
                </div>',
                'settings' => [
                    'trigger_scroll_percent' => 80,
                    'popup_position' => 'bottom-left',
                    'popup_width' => '250px',
                    'popup_animation' => 'slide-up'
                ]
            ],
            'survey' => [
                'name' => __('Quick Survey', 'master-addons'),
                'description' => __('Collect feedback with a quick survey', 'master-addons'),
                'type' => 'modal',
                'category' => 'feedback',
                'content' => '<div class="ma-popup-template-survey">
                    <h3>Quick Survey</h3>
                    <p>Help us improve! How would you rate your experience?</p>
                    <div class="ma-survey-options">
                        <label><input type="radio" name="rating" value="excellent"> Excellent</label>
                        <label><input type="radio" name="rating" value="good"> Good</label>
                        <label><input type="radio" name="rating" value="fair"> Fair</label>
                        <label><input type="radio" name="rating" value="poor"> Poor</label>
                    </div>
                    <textarea placeholder="Additional comments (optional)" rows="3"></textarea>
                    <button type="submit">Submit Feedback</button>
                </div>',
                'settings' => [
                    'popup_width' => '400px',
                    'popup_animation' => 'fade'
                ]
            ]
        ];
    }
    
    public function render() {
        $categories = $this->get_categories();
        ?>
        <div class="wrap ma-popup-templates">
            <h1><?php esc_html_e('Popup Templates', 'master-addons'); ?></h1>
            <p><?php esc_html_e('Choose from our pre-designed templates to get started quickly.', 'master-addons'); ?></p>

            <div class="ma-template-categories">
                <button class="ma-category-filter active" data-category="all">
                    <?php esc_html_e('All Templates', 'master-addons'); ?>
                </button>
                <?php foreach ($categories as $key => $label) : ?>
                    <button class="ma-category-filter" data-category="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div class="ma-templates-grid">
                <?php foreach ($this->templates as $key => $template) : ?>
                    <div class="ma-template-card" data-category="<?php echo esc_attr($template['category']); ?>">
                        <div class="ma-template-preview">
                            <div class="ma-template-content">
                                <?php echo wp_kses_post( $template['content'] ); ?>
                            </div>
                        </div>
                        <div class="ma-template-info">
                            <h3><?php echo esc_html($template['name']); ?></h3>
                            <p><?php echo esc_html($template['description']); ?></p>
                            <div class="ma-template-meta">
                                <span class="ma-template-type"><?php echo esc_html($this->get_type_label($template['type'])); ?></span>
                                <span class="ma-template-category"><?php echo esc_html($categories[$template['category']]); ?></span>
                            </div>
                            <button class="button button-primary ma-use-template" 
                                    data-template="<?php echo esc_attr($key); ?>">
                                <?php esc_html_e('Use This Template', 'master-addons'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    private function get_categories() {
        return [
            'lead-generation' => __('Lead Generation', 'master-addons'),
            'sales' => __('Sales & Promotions', 'master-addons'),
            'compliance' => __('Compliance', 'master-addons'),
            'informational' => __('Informational', 'master-addons'),
            'social' => __('Social Media', 'master-addons'),
            'media' => __('Media', 'master-addons'),
            'feedback' => __('Feedback', 'master-addons'),
        ];
    }
    
    private function get_type_label($type) {
        $types = [
            'notification' => __('Notification Bar', 'master-addons'),
            'modal' => __('Modal', 'master-addons'),
            'slide-in' => __('Slide-in', 'master-addons'),
            'full-screen' => __('Full Screen', 'master-addons'),
            'corner' => __('Corner Popup', 'master-addons'),
        ];
        
        return isset($types[$type]) ? $types[$type] : $type;
    }
    
    public function get_template($key) {
        return isset($this->templates[$key]) ? $this->templates[$key] : null;
    }
}