<?php

namespace MasterAddons\Inc\Classes\Notifications;

use MasterAddons\Inc\Classes\Notifications\Model\Notice;

if (!class_exists('Pro_Sale_Notice')) {
    /**
     * Pro Sale Notice with Confetti Animation
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Pro_Sale_Notice extends Notice
    {

        public $color = 'success';

        /**
         * Pro Sale Notice Constructor
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Notice Header with Custom Structure
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function notice_header()
        { ?>
            <div class="notice jltma-plugin-sale-notice is-dismissible">
                <div class="jltma-sale-notice-content">
        <?php
        }

        /**
         * Notice Content
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function notice_content()
        {
            ?>
            <h3><span>Flash Sale</span><br> Master Addons Pro</h3>
            <ul>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    100+ Premium Elementor Widgets
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    50+ Designer Made Templates
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    Advanced Header Footer Builder <a class="jltma-demo-tutorial" href="https://www.youtube.com/watch?v=kE1zmi3fxh8" target="_blank">View Demo</a>
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    Advanced WooCommerce Elements
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    Creative Button & Effects
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    Premium Support & Updates
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    Advanced Extensions & Addons
                </li>
                <li>
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'check-mark.png'); ?>" alt="Check">
                    Mega Menu Builder and much more...
                </li>
            </ul>
            <p>
                Hurry up! Upgrade within the <strong>next 24 hours</strong> and get a
                <strong>25% Discount</strong>.<br><br>
                Use Promo Code: &nbsp;&nbsp;&nbsp;<strong style="border: 1px dashed #C3C4C7;padding: 2px 10px;">MASALE25</strong>
            </p>
            <br>
            <div class="jltma-sale-buttons">
                <a href="https://master-addons.com/?ref=ma-plugin-backend-salebanner-upgrade-pro#purchasepro" target="_blank" class="jltma-upgrade-to-pro-button button button-secondary">
                    Upgrade to Pro <span class="dashicons dashicons-arrow-right-alt"></span>
                </a>
                <a href="#" class="jltma-upgrade-to-pro-button button button-secondary jltma-remind-later">Remind Me Later</a>
            </div>
            <?php
        }

        /**
         * Notice Footer with Custom Structure
         *
         * @return void
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function notice_footer()
        {
            ?>
                </div>
                <div class="jltma-sale-image-wrap">
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'sale-banner-25.png'); ?>" alt="Sale Banner">
                </div>
                <canvas id="jltma-sale-confetti"></canvas>
                <button type="button" class="notice-dismiss jltma-notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
            <?php
        }

        /**
         * Core Script with Confetti Animation
         *
         * @param [type] $trigger_time .
         *
         * @return void
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function core_script($trigger_time)
        {
            ?>
            <script>
                function jltma_sale_notice_action(evt, $this, action_type) {
                    if (evt) evt.preventDefault();
                    $this.closest('.jltma-plugin-sale-notice').slideUp(200);

                    jQuery.post('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                        action: 'jltma_notification_action',
                        _wpnonce: '<?php echo esc_js(wp_create_nonce('jltma_notification_nonce')); ?>',
                        action_type: action_type,
                        notification_type: 'notice',
                        trigger_time: '<?php echo esc_attr($trigger_time); ?>'
                    });
                }

                // Notice Dismiss
                jQuery('body').on('click', '.jltma-plugin-sale-notice .jltma-notice-dismiss', function(evt) {
                    jltma_sale_notice_action(evt, jQuery(this), 'dismiss');
                });

                // Remind Later
                jQuery('body').on('click', '.jltma-plugin-sale-notice .jltma-remind-later', function(evt) {
                    jltma_sale_notice_action(evt, jQuery(this), 'remind_later');
                });

                // Sale Confetti Animation
                function initJltmaSaleConfetti() {
                    const canvas = document.getElementById('jltma-sale-confetti');
                    if (!canvas) return;

                    const ctx = canvas.getContext('2d');
                    const confetti = [];
                    const colors = ['#ffd700', '#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'];

                    // Set canvas size to match notice
                    canvas.width = canvas.parentElement.offsetWidth;
                    canvas.height = canvas.parentElement.offsetHeight;

                    // Enhanced confetti particle class
                    class SaleConfettiPiece {
                        constructor() {
                            this.x = Math.random() * canvas.width;
                            this.y = -10;
                            this.vx = (Math.random() - 0.5) * 4;
                            this.vy = Math.random() * 4 + 2;
                            this.color = colors[Math.floor(Math.random() * colors.length)];
                            this.size = Math.random() * 6 + 3;
                            this.rotation = Math.random() * 360;
                            this.rotationSpeed = (Math.random() - 0.5) * 8;
                            this.opacity = 0.8 + Math.random() * 0.2;
                            this.shape = Math.random() > 0.5 ? 'rect' : 'circle';
                        }

                        update() {
                            this.x += this.vx;
                            this.y += this.vy;
                            this.rotation += this.rotationSpeed;

                            // Fade out as it falls
                            this.opacity -= 0.002;

                            if (this.y > canvas.height + 20 || this.opacity <= 0) {
                                this.y = -10;
                                this.x = Math.random() * canvas.width;
                                this.opacity = 0.8 + Math.random() * 0.2;
                            }
                        }

                        draw() {
                            ctx.save();
                            ctx.globalAlpha = this.opacity;
                            ctx.translate(this.x, this.y);
                            ctx.rotate(this.rotation * Math.PI / 180);
                            ctx.fillStyle = this.color;

                            if (this.shape === 'circle') {
                                ctx.beginPath();
                                ctx.arc(0, 0, this.size / 2, 0, Math.PI * 2);
                                ctx.fill();
                            } else {
                                ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
                            }
                            
                            ctx.restore();
                        }
                    }

                    // Create more confetti for sale notice
                    for (let i = 0; i < 80; i++) {
                        confetti.push(new SaleConfettiPiece());
                    }

                    // Animation loop
                    function animateSale() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        
                        confetti.forEach(piece => {
                            piece.update();
                            piece.draw();
                        });

                        requestAnimationFrame(animateSale);
                    }

                    animateSale();
                }

                // Initialize sale confetti
                jQuery(document).ready(function() {
                    setTimeout(initJltmaSaleConfetti, 800);
                });
            </script>

            <style>
                .jltma-plugin-sale-notice {
                    position: relative;
                    display: flex;
                    align-items: stretch;
                    padding: 25px;
                    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
                    color: white;
                    border: none;
                    border-radius: 12px;
                    margin: 15px 0;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(238, 90, 36, 0.3);
                }

                .jltma-sale-notice-content {
                    flex: 1;
                    z-index: 2;
                }

                .jltma-sale-notice-content h3 {
                    font-size: 28px;
                    margin: 0 0 20px;
                    color: white;
                    font-weight: 700;
                }

                .jltma-sale-notice-content h3 span {
                    background: rgba(255, 255, 255, 0.25);
                    padding: 6px 16px;
                    border-radius: 25px;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                }

                .jltma-sale-notice-content ul {
                    list-style: none;
                    margin: 20px 0;
                    padding: 0;
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 10px;
                }

                .jltma-sale-notice-content ul li {
                    display: flex;
                    align-items: center;
                    margin: 8px 0;
                    font-size: 14px;
                    line-height: 1.4;
                }

                .jltma-sale-notice-content ul li img {
                    width: 16px;
                    height: 16px;
                    margin-right: 10px;
                    flex-shrink: 0;
                }

                .jltma-demo-tutorial {
                    color: #ffd700;
                    text-decoration: none;
                    font-weight: 600;
                    margin-left: 5px;
                }

                .jltma-demo-tutorial:hover {
                    color: white;
                }

                .jltma-sale-notice-content p {
                    margin: 20px 0;
                    line-height: 1.6;
                    color: rgba(255, 255, 255, 0.95);
                    font-size: 16px;
                }

                .jltma-sale-buttons {
                    display: flex;
                    gap: 15px;
                    flex-wrap: wrap;
                }

                .jltma-upgrade-to-pro-button {
                    background: white !important;
                    color: #ee5a24 !important;
                    border: none !important;
                    padding: 12px 20px !important;
                    border-radius: 25px !important;
                    font-weight: 600 !important;
                    text-decoration: none !important;
                    transition: all 0.3s ease !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 5px !important;
                }

                .jltma-upgrade-to-pro-button:hover {
                    background: #ffd700 !important;
                    transform: translateY(-2px) !important;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
                }

                .jltma-remind-later {
                    background: rgba(255, 255, 255, 0.2) !important;
                    color: white !important;
                }

                .jltma-remind-later:hover {
                    background: rgba(255, 255, 255, 0.3) !important;
                    color: white !important;
                }

                .jltma-sale-image-wrap {
                    margin-left: 30px;
                    flex-shrink: 0;
                    z-index: 2;
                    display: flex;
                    align-items: center;
                }

                .jltma-sale-image-wrap img {
                    max-width: 250px;
                    height: auto;
                    border-radius: 12px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                }

                #jltma-sale-confetti {
                    position: absolute;
                    top: 0;
                    left: 0;
                    pointer-events: none;
                    z-index: 1;
                }

                .jltma-plugin-sale-notice .notice-dismiss {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    color: white;
                    z-index: 3;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 50%;
                }

                .jltma-plugin-sale-notice .notice-dismiss:before {
                    color: white;
                    font-size: 16px;
                }

                .jltma-plugin-sale-notice .notice-dismiss:hover {
                    background: rgba(255, 255, 255, 0.3);
                }

                .jltma-plugin-sale-notice .notice-dismiss:hover:before {
                    color: white;
                }

                @media (max-width: 1024px) {
                    .jltma-plugin-sale-notice {
                        flex-direction: column;
                    }

                    .jltma-sale-image-wrap {
                        margin-left: 0;
                        margin-top: 25px;
                        text-align: center;
                    }

                    .jltma-sale-notice-content ul {
                        grid-template-columns: 1fr;
                    }
                }

                @media (max-width: 768px) {
                    .jltma-sale-notice-content h3 {
                        font-size: 24px;
                    }

                    .jltma-sale-buttons {
                        justify-content: center;
                    }

                    .jltma-upgrade-to-pro-button {
                        padding: 10px 16px !important;
                        font-size: 14px !important;
                    }
                }
            </style>
            <?php
        }

        /**
         * Intervals
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function intervals()
        {
            return array(0);
        }
    }
}