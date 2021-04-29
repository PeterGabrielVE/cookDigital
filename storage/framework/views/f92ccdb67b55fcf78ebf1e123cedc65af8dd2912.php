<!DOCTYPE html>
<html lang="en">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php
    $logo=asset(Storage::url('logo/'));
    $favicon=Utility::getValByName('company_favicon');
?>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title> <?php echo $__env->yieldContent('page-title'); ?> &dash; <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'WorkGo')); ?></title>
    <link rel="icon" href="<?php echo e($logo.'/'.(isset($favicon) && !empty($favicon)?$favicon:'favicon.png')); ?>" type="image">

    <?php echo $__env->yieldPushContent('css-page'); ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/bootstrap-daterangepicker/daterangepicker.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/animate.css/animate.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/site.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ac.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/datatables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/stylesheet.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>">
</head>

<body class="application application-offset">
<div class="container-fluid container-application">
    <?php echo $__env->make('partials.admin.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="main-content position-relative">
        <?php echo $__env->make('partials.admin.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="page-content">
            <div class="page-title">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                        <div class="d-inline-block">
                            <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo $__env->yieldContent('page-title'); ?></h5>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                        <?php echo $__env->yieldContent('action-button'); ?>
                    </div>
                </div>
            </div>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <footer class="main-footer bottom-0 px-4 py-4">
            <div class="footer-left">
                <?php echo e(__('Copyright')); ?> &copy; <?php echo e((Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'WorkGo')); ?> <?php echo e(date('Y')); ?>

            </div>
            <div class="footer-right">
            </div>
        </footer>
    </div>
</div>

<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div>
                <h4 class="h4 font-weight-400 float-left modal-title"></h4>
                <a href="#" class="more-text widget-text float-right close-icon" data-dismiss="modal" aria-label="Close"><?php echo e(__('Close')); ?></a>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div id="omnisearch" class="omnisearch">
    <div class="container">
        <div class="omnisearch-form">
            <div class="form-group">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control search_keyword" placeholder="<?php echo e(__('Type and search By Project & Tasks.')); ?>">
                </div>
            </div>
        </div>
        <div class="omnisearch-suggestions">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="list-unstyled mb-0 search-output text-sm">
                        <li>
                            <a class="list-link pl-4" href="#">
                                <i class="fas fa-search"></i>
                                <span><?php echo e(__('Type and search By Project & Tasks.')); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- General JS Scripts -->
<script src="<?php echo e(asset('assets/js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/site.core.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/progressbar.js/dist/progressbar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/chart/Chart.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/chart/Chart.extension.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/moment/min/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/site.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/datatables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/bootstrap-daterangepicker/daterangepicker.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/select2/dist/js/select2.min.js')); ?>"></script>



<?php if(\Auth::user()->type != 'super admin'): ?>
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <script>
        $(document).ready(function () {
            pushNotification('<?php echo e(Auth::id()); ?>');
        });

        function pushNotification(id) {

            // ajax setup form csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = false;

            var pusher = new Pusher('<?php echo e(env('PUSHER_APP_KEY')); ?>', {
                cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
                forceTLS: true
            });

            var channel = pusher.subscribe('send_notification');
            channel.bind('notification', function (data) {
                if (id == data.user_id) {
                    $(".notification-toggle").addClass('beep');
                    $(".notification-dropdown #notification-list").prepend(data.html);
                }
            });

            // Pusher Message
            var msgChannel = pusher.subscribe('my-channel');
            msgChannel.bind('my-chat', function (data) {
                console.log(data);
                if (id == data.to) {
                    getChat();
                }
            });
        }

        // Mark As Read Notification
        $(document).on("click", ".mark_all_as_read", function () {
            $.ajax({
                url: '<?php echo e(route('notification.seen',\Auth::user()->id)); ?>',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.notification-dropdown #notification-list').html('');
                    $(".notification-toggle").removeClass('beep');
                }
            })
        });

        // Get chat for top ox
        function getChat() {
            $.ajax({
                url: '<?php echo e(route('message.data')); ?>',
                type: "get",
                cache: false,
                success: function (data) {
                    if (data.length != 0) {
                        $(".message-toggle-msg").addClass('beep');
                        $(".dropdown-list-message-msg").html(data);
                    }
                }
            })
        }

        getChat();

        $(document).on("click", ".mark_all_as_read_message", function () {
            $.ajax({
                url: '<?php echo e(route('message.seen')); ?>',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.dropdown-list-message-msg').html('');
                    $(".message-toggle-msg").removeClass('beep');
                }
            })
        });
    </script>
<?php endif; ?>

<script>
    var date_picker_locale = {
        format: 'YYYY-MM-DD',
        daysOfWeek: [
            "<?php echo e(__('Sun')); ?>",
            "<?php echo e(__('Mon')); ?>",
            "<?php echo e(__('Tue')); ?>",
            "<?php echo e(__('Wed')); ?>",
            "<?php echo e(__('Thu')); ?>",
            "<?php echo e(__('Fri')); ?>",
            "<?php echo e(__('Sat')); ?>"
        ],
        monthNames: [
            "<?php echo e(__('January')); ?>",
            "<?php echo e(__('February')); ?>",
            "<?php echo e(__('March')); ?>",
            "<?php echo e(__('April')); ?>",
            "<?php echo e(__('May')); ?>",
            "<?php echo e(__('June')); ?>",
            "<?php echo e(__('July')); ?>",
            "<?php echo e(__('August')); ?>",
            "<?php echo e(__('September')); ?>",
            "<?php echo e(__('October')); ?>",
            "<?php echo e(__('November')); ?>",
            "<?php echo e(__('December')); ?>"
        ],
    };

    $(document).ready(function () {
        if ($('.dataTable').length > 0) {
            $(".dataTable").dataTable({
                language: {
                    "lengthMenu": "<?php echo e(__('Display')); ?> _MENU_ <?php echo e(__('records per page')); ?>",
                    "zeroRecords": "<?php echo e(__('No data available in table')); ?>",
                    "info": "<?php echo e(__('Showing page')); ?> _PAGE_ <?php echo e(__('of')); ?> _PAGES_",
                    "infoEmpty": "<?php echo e(__('No page available')); ?>",
                    "infoFiltered": "(<?php echo e(__('filtered from')); ?> _MAX_ <?php echo e(__('total records')); ?>)",
                    "paginate": {
                        "previous": "<?php echo e(__('Previous')); ?>",
                        "next": "<?php echo e(__('Next')); ?>",
                        "last": "<?php echo e(__('Last')); ?>"
                    }
                },
            })
        }

        <?php if(Auth::user()->type != 'super admin'): ?>
        $(document).on('keyup', '.search_keyword', function () {
            search_data($(this).val());
        });
        <?php endif; ?>
    })

    <?php if(Auth::user()->type != 'super admin'): ?>
    // Common main search
    var currentRequest = null;

    function search_data(keyword = '') {
        currentRequest = $.ajax({
            url: '<?php echo e(route('search.json')); ?>',
            data: {keyword: keyword},
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                $('.search-output').html(data);
            }
        });
    }
    <?php endif; ?>
</script>

<script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>

<?php echo $__env->yieldPushContent('script-page'); ?>

<?php if($message = Session::get('success')): ?>
    <script>show_toastr('<?php echo e(__("Success")); ?>', '<?php echo $message; ?>', 'success')</script>
<?php endif; ?>

<?php if($message = Session::get('error')): ?>
    <script>show_toastr('Error', '<?php echo $message; ?>', 'error')</script>
<?php endif; ?>

<?php if($message = Session::get('info')): ?>
    <script>show_toastr('<?php echo e(__("Info")); ?>', '<?php echo $message; ?>', 'info')</script>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\wamp\www\workGo\resources\views/layouts/admin.blade.php ENDPATH**/ ?>