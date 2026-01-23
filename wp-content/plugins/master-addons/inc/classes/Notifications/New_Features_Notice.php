<?php

namespace MasterAddons\Inc\Classes\Notifications;

use MasterAddons\Inc\Classes\Notifications\Model\Notice;

if (!class_exists('New_Features_Notice')) {
    /**
     * New Features Notice with Confetti Animation
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class New_Features_Notice extends Notice
    {

        public $color = 'info';

        /**
         * New Features Notice Constructor
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
            <div class="notice jltma-plugin-update-notice is-dismissible">
                <div class="jltma-plugin-update-notice-logo">
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'logo.svg'); ?>" alt="Master Addons Logo">
                </div>
                <div class="jltma-notice-content">
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
            <h3><span>New Features</span><br> Introducing New Widgets</h3>
            <p>We are excited to announce that we have added new Widgets, Template Kits, and other Elementor<br> features to enhance your website building experience. Stay tuned for the weekly updates!</p>
            <ul class="jltma-new-widgets-list">
                <li><a target="_blank" href="https://master-addons.com/elementor-widgets/image-hover-effects/?ref=ma-plugin-backend-update-notice">Image Hover Effects</a></li>
                <li><a target="_blank" href="https://master-addons.com/elementor-widgets/mega-menu/?ref=ma-plugin-backend-update-notice">Mega Menu</a></li>
                <li><a target="_blank" href="https://master-addons.com/elementor-widgets/creative-buttons/?ref=ma-plugin-backend-update-notice">Creative Buttons</a></li>
                <li><a target="_blank" href="https://master-addons.com/elementor-widgets/timeline/?ref=ma-plugin-backend-update-notice">Timeline Widget</a></li>
                <li><a target="_blank" href="https://master-addons.com/elementor-widgets/blog-element/?ref=ma-plugin-backend-update-notice">Blog Element</a></li>
            </ul>
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
                <div class="jltma-notice-image-wrap">
                    <img src="<?php echo esc_url(JLTMA_IMAGE_DIR . 'banner.png'); ?>" alt="New Features">
                </div>
                <canvas id="jltma-notice-confetti" width="1478" height="312"></canvas>
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
                function jltma_notice_action(evt, $this, action_type) {
                    if (evt) evt.preventDefault();
                    $this.closest('.jltma-plugin-update-notice').slideUp(200);

                    jQuery.post('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                        action: 'jltma_notification_action',
                        _wpnonce: '<?php echo esc_js(wp_create_nonce('jltma_notification_nonce')); ?>',
                        action_type: action_type,
                        notification_type: 'notice',
                        trigger_time: '<?php echo esc_attr($trigger_time); ?>'
                    });
                }

                // Notice Dismiss
                jQuery('body').on('click', '.jltma-plugin-update-notice .jltma-notice-dismiss', function(evt) {
                    jltma_notice_action(evt, jQuery(this), 'dismiss');
                });

                // Confetti Animation
                function initJltmaConfetti() {
                    const canvas = document.getElementById('jltma-notice-confetti');
                    if (!canvas) return;

                    const ctx = canvas.getContext('2d');
                    const confetti = [];
                    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'];

                    // Set canvas size
                    canvas.width = canvas.offsetWidth;
                    canvas.height = canvas.offsetHeight;

                    // Confetti particle class
                    class ConfettiPiece {
                        constructor() {
                            this.x = Math.random() * canvas.width;
                            this.y = -10;
                            this.vx = (Math.random() - 0.5) * 3;
                            this.vy = Math.random() * 3 + 2;
                            this.color = colors[Math.floor(Math.random() * colors.length)];
                            this.size = Math.random() * 5 + 2;
                            this.rotation = Math.random() * 360;
                            this.rotationSpeed = (Math.random() - 0.5) * 5;
                        }

                        update() {
                            this.x += this.vx;
                            this.y += this.vy;
                            this.rotation += this.rotationSpeed;

                            if (this.y > canvas.height + 10) {
                                this.y = -10;
                                this.x = Math.random() * canvas.width;
                            }
                        }

                        draw() {
                            ctx.save();
                            ctx.translate(this.x, this.y);
                            ctx.rotate(this.rotation * Math.PI / 180);
                            ctx.fillStyle = this.color;
                            ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
                            ctx.restore();
                        }
                    }

                    // Create confetti pieces
                    for (let i = 0; i < 50; i++) {
                        confetti.push(new ConfettiPiece());
                    }

                    // Animation loop
                    function animate() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        confetti.forEach(piece => {
                            piece.update();
                            piece.draw();
                        });

                        requestAnimationFrame(animate);
                    }

                    animate();
                }

                // Initialize confetti when document is ready
                jQuery(document).ready(function() {
                    setTimeout(initJltmaConfetti, 500);
                });
            </script>

            <style>
                .jltma-plugin-update-notice {
                    position: relative;
                    display: flex;
                    align-items: center;
                    padding: 20px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    margin: 15px 0;
                    overflow: hidden;
                }

                .jltma-plugin-update-notice-logo {
                    margin-right: 20px;
                    flex-shrink: 0;
                }

                .jltma-plugin-update-notice-logo img {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                }

                .jltma-notice-content {
                    flex: 1;
                    z-index: 2;
                }

                .jltma-notice-content h3 {
                    font-size: 24px;
                    margin: 0 0 15px;
                    color: white;
                }

                .jltma-notice-content h3 span {
                    background: rgba(255, 255, 255, 0.2);
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                .jltma-notice-content p {
                    margin: 15px 0;
                    line-height: 1.6;
                    color: rgba(255, 255, 255, 0.9);
                }

                .jltma-new-widgets-list {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 15px;
                }

                .jltma-new-widgets-list li {
                    margin: 0;
                }

                .jltma-new-widgets-list a {
                    color: white;
                    text-decoration: none;
                    background: rgba(255, 255, 255, 0.2);
                    padding: 8px 15px;
                    border-radius: 20px;
                    font-size: 13px;
                    transition: all 0.3s ease;
                    display: inline-block;
                }

                .jltma-new-widgets-list a:hover {
                    background: rgba(255, 255, 255, 0.3);
                    transform: translateY(-2px);
                }

                .jltma-notice-image-wrap {
                    margin-left: 20px;
                    flex-shrink: 0;
                    z-index: 2;
                }

                .jltma-notice-image-wrap img {
                    max-width: 200px;
                    height: auto;
                    border-radius: 8px;
                }

                #jltma-notice-confetti {
                    position: absolute;
                    top: 0;
                    left: 0;
                    pointer-events: none;
                    z-index: 1;
                }

                .jltma-plugin-update-notice .notice-dismiss {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    color: white;
                    z-index: 3;
                }

                .jltma-plugin-update-notice .notice-dismiss:before {
                    color: white;
                }

                .jltma-plugin-update-notice .notice-dismiss:hover:before {
                    color: rgba(255, 255, 255, 0.7);
                }

                @media (max-width: 768px) {
                    .jltma-plugin-update-notice {
                        flex-direction: column;
                        text-align: center;
                    }

                    .jltma-plugin-update-notice-logo {
                        margin-right: 0;
                        margin-bottom: 20px;
                    }

                    .jltma-notice-image-wrap {
                        margin-left: 0;
                        margin-top: 20px;
                    }

                    .jltma-new-widgets-list {
                        justify-content: center;
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
