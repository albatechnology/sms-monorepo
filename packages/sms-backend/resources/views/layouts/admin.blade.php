<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
        rel="stylesheet" />
    <link href="{{ asset('css/adminltev3.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
    <livewire:styles />
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.1/dist/alpine.min.js" defer></script>
</head>

<body class="sidebar-mini layout-fixed" style="height: auto;">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            {{-- @if (count(config('panel.available_languages', [])) > 1)
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach (config('panel.available_languages') as $langLocale => $langName)
                                <a class="dropdown-item"
                                    href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }}
                                    ({{ $langName }})</a>
                            @endforeach
                        </div>
                    </li>
                </ul>
            @endif --}}
            <ul class="navbar-nav ml-auto">
                <li class="nav-item align-self-center">
                    {{ auth()->user()->name }}
                </li>
                <li class="nav-item dropdown notifications-menu">
                    <a href="#" class="nav-link" data-toggle="dropdown">
                        <i class="far fa-bell"></i>
                        @php
                            $alertsCount = Auth::user()
                                ->userUserAlerts()
                                ->where('read', false)
                                ->count();
                        @endphp
                        @if ($alertsCount > 0)
                            <span class="badge badge-warning navbar-badge">
                                {{ $alertsCount }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        @if (count(
                                $alerts = Auth::user()->userUserAlerts()->withPivot('read')->limit(10)->orderBy('created_at', 'ASC')->get()->reverse()) > 0)
                            @foreach ($alerts as $alert)
                                <div class="dropdown-item">
                                    <a href="{{ $alert->alert_link ? $alert->alert_link : '#' }}" target="_blank"
                                        rel="noopener noreferrer">
                                        @if ($alert->pivot->read === 0)
                                            <strong>
                                        @endif
                                        {{ $alert->alert_text }}
                                        @if ($alert->pivot->read === 0)
                                            </strong>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center">
                                {{ trans('global.no_alerts') }}
                            </div>
                        @endif
                    </div>
                </li>
            </ul>
        </nav>
        @include('partials.menu')
        <div class="content-wrapper" style="min-height: 917px;">
            <!-- Main content -->
            <section class="content" style="padding-top: 20px">
                @if (session('message'))
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                @endif
                @if ($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </section>
            <!-- /.content -->
        </div>
        <footer class="main-footer">
            <strong> &copy;</strong> {{ trans('global.allRightsReserved') }}
        </footer>
        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        $(function() {
            $('.form-loading').on('submit', function() {
                $(this).find(':submit').attr('disabled', true).text('Loading...')
            });
            let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
            let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
            let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
            let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
            let printButtonTrans = '{{ trans('global.datatables.print') }}'
            let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'
            let selectAllButtonTrans = '{{ trans('global.select_all') }}'
            let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

            let languages = {
                'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json',
                'id': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json'
            };

            $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
                className: 'btn'
            })
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: languages['{{ app()->getLocale() }}']
                },
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }, {
                    orderable: false,
                    searchable: false,
                    targets: -1
                }],
                select: {
                    style: 'multi+shift',
                    selector: 'td:first-child'
                },
                lengthMenu: [
                    [10, 25, 50, 100, 500, 1000, 2500, -1],
                    [10, 25, 50, 100, 500, 1000, 2500, "All"]
                ],
                order: [],
                scrollX: true,
                pageLength: 100,
                dom: 'lBfrtip<"actions">',
                buttons: [{
                        extend: 'selectAll',
                        className: 'btn-primary',
                        text: selectAllButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        },
                        action: function(e, dt) {
                            e.preventDefault()
                            dt.rows().deselect();
                            dt.rows({
                                search: 'applied'
                            }).select();
                        }
                    },
                    {
                        extend: 'selectNone',
                        className: 'btn-primary',
                        text: selectNoneButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn-default',
                        text: copyButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-default',
                        text: csvButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-default',
                        text: excelButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-default',
                        text: pdfButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn-default',
                        text: printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn-default',
                        text: colvisButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            $.fn.dataTable.ext.classes.sPageButton = '';
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".notifications-menu").on('click', function() {
                if (!$(this).hasClass('open')) {
                    $('.notifications-menu .label-warning').hide();
                    $.get('/admin/user-alerts/read');
                }
            });
        });
    </script>
    <script>
        /*!
         * AdminLTE v3.0.0 (https://adminlte.io)
         * Copyright 2014-2019 Colorlib <http://colorlib.com>
         * Licensed under MIT (https://github.com/ColorlibHQ/AdminLTE/blob/master/LICENSE)
         */
        (function(global, factory) {
            typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
                typeof define === 'function' && define.amd ? define(['exports'], factory) :
                (global = global || self, factory(global.adminlte = {}));
        }(this, function(exports) {
            'use strict';

            /**
             * --------------------------------------------
             * AdminLTE ControlSidebar.js
             * License MIT
             * --------------------------------------------
             */
            var ControlSidebar = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'ControlSidebar';
                var DATA_KEY = 'lte.controlsidebar';
                var EVENT_KEY = "." + DATA_KEY;
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    COLLAPSED: "collapsed" + EVENT_KEY,
                    EXPANDED: "expanded" + EVENT_KEY
                };
                var Selector = {
                    CONTROL_SIDEBAR: '.control-sidebar',
                    CONTROL_SIDEBAR_CONTENT: '.control-sidebar-content',
                    DATA_TOGGLE: '[data-widget="control-sidebar"]',
                    CONTENT: '.content-wrapper',
                    HEADER: '.main-header',
                    FOOTER: '.main-footer'
                };
                var ClassName = {
                    CONTROL_SIDEBAR_ANIMATE: 'control-sidebar-animate',
                    CONTROL_SIDEBAR_OPEN: 'control-sidebar-open',
                    CONTROL_SIDEBAR_SLIDE: 'control-sidebar-slide-open',
                    LAYOUT_FIXED: 'layout-fixed',
                    NAVBAR_FIXED: 'layout-navbar-fixed',
                    NAVBAR_SM_FIXED: 'layout-sm-navbar-fixed',
                    NAVBAR_MD_FIXED: 'layout-md-navbar-fixed',
                    NAVBAR_LG_FIXED: 'layout-lg-navbar-fixed',
                    NAVBAR_XL_FIXED: 'layout-xl-navbar-fixed',
                    FOOTER_FIXED: 'layout-footer-fixed',
                    FOOTER_SM_FIXED: 'layout-sm-footer-fixed',
                    FOOTER_MD_FIXED: 'layout-md-footer-fixed',
                    FOOTER_LG_FIXED: 'layout-lg-footer-fixed',
                    FOOTER_XL_FIXED: 'layout-xl-footer-fixed'
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var ControlSidebar =
                    /*#__PURE__*/
                    function() {
                        function ControlSidebar(element, config) {
                            this._element = element;
                            this._config = config;

                            this._init();
                        } // Public


                        var _proto = ControlSidebar.prototype;

                        _proto.show = function show() {
                            // Show the control sidebar
                            if (this._config.controlsidebarSlide) {
                                $('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
                                $('body').removeClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(
                                    function() {
                                        $(Selector.CONTROL_SIDEBAR).hide();
                                        $('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
                                        $(this).dequeue();
                                    });
                            } else {
                                $('body').removeClass(ClassName.CONTROL_SIDEBAR_OPEN);
                            }

                            var expandedEvent = $.Event(Event.EXPANDED);
                            $(this._element).trigger(expandedEvent);
                        };

                        _proto.collapse = function collapse() {
                            // Collapse the control sidebar
                            if (this._config.controlsidebarSlide) {
                                $('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
                                $(Selector.CONTROL_SIDEBAR).show().delay(10).queue(function() {
                                    $('body').addClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300)
                                        .queue(function() {
                                            $('html').removeClass(ClassName
                                                .CONTROL_SIDEBAR_ANIMATE);
                                            $(this).dequeue();
                                        });
                                    $(this).dequeue();
                                });
                            } else {
                                $('body').addClass(ClassName.CONTROL_SIDEBAR_OPEN);
                            }

                            var collapsedEvent = $.Event(Event.COLLAPSED);
                            $(this._element).trigger(collapsedEvent);
                        };

                        _proto.toggle = function toggle() {
                            var shouldOpen = $('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body')
                                .hasClass(ClassName.CONTROL_SIDEBAR_SLIDE);

                            if (shouldOpen) {
                                // Open the control sidebar
                                this.show();
                            } else {
                                // Close the control sidebar
                                this.collapse();
                            }
                        } // Private
                        ;

                        _proto._init = function _init() {
                            var _this = this;

                            this._fixHeight();

                            this._fixScrollHeight();

                            $(window).resize(function() {
                                _this._fixHeight();

                                _this._fixScrollHeight();
                            });
                            $(window).scroll(function() {
                                if ($('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body')
                                    .hasClass(ClassName.CONTROL_SIDEBAR_SLIDE)) {
                                    _this._fixScrollHeight();
                                }
                            });
                        };

                        _proto._fixScrollHeight = function _fixScrollHeight() {
                            var heights = {
                                scroll: $(document).height(),
                                window: $(window).height(),
                                header: $(Selector.HEADER).outerHeight(),
                                footer: $(Selector.FOOTER).outerHeight()
                            };
                            var positions = {
                                bottom: Math.abs(heights.window + $(window).scrollTop() - heights
                                    .scroll),
                                top: $(window).scrollTop()
                            };
                            var navbarFixed = false;
                            var footerFixed = false;

                            if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
                                if ($('body').hasClass(ClassName.NAVBAR_FIXED) || $('body').hasClass(
                                        ClassName.NAVBAR_SM_FIXED) || $('body').hasClass(ClassName
                                        .NAVBAR_MD_FIXED) || $('body').hasClass(ClassName
                                        .NAVBAR_LG_FIXED) || $('body').hasClass(ClassName
                                        .NAVBAR_XL_FIXED)) {
                                    if ($(Selector.HEADER).css("position") === "fixed") {
                                        navbarFixed = true;
                                    }
                                }

                                if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(
                                        ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName
                                        .FOOTER_MD_FIXED) || $('body').hasClass(ClassName
                                        .FOOTER_LG_FIXED) || $('body').hasClass(ClassName
                                        .FOOTER_XL_FIXED)) {
                                    if ($(Selector.FOOTER).css("position") === "fixed") {
                                        footerFixed = true;
                                    }
                                }

                                if (positions.top === 0 && positions.bottom === 0) {
                                    $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
                                    $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
                                    $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' +
                                        Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window -
                                        (heights.header + heights.footer));
                                } else if (positions.bottom <= heights.footer) {
                                    if (footerFixed === false) {
                                        $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer - positions
                                            .bottom);
                                        $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' +
                                            Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights
                                            .window - (heights.footer - positions.bottom));
                                    } else {
                                        $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
                                    }
                                } else if (positions.top <= heights.header) {
                                    if (navbarFixed === false) {
                                        $(Selector.CONTROL_SIDEBAR).css('top', heights.header - positions
                                            .top);
                                        $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' +
                                            Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights
                                            .window - (heights.header - positions.top));
                                    } else {
                                        $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
                                    }
                                } else {
                                    if (navbarFixed === false) {
                                        $(Selector.CONTROL_SIDEBAR).css('top', 0);
                                        $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' +
                                            Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights
                                            .window);
                                    } else {
                                        $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
                                    }
                                }
                            }
                        };

                        _proto._fixHeight = function _fixHeight() {
                            var heights = {
                                window: $(window).height(),
                                header: $(Selector.HEADER).outerHeight(),
                                footer: $(Selector.FOOTER).outerHeight()
                            };

                            if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
                                var sidebarHeight = heights.window - heights.header;

                                if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(
                                        ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName
                                        .FOOTER_MD_FIXED) || $('body').hasClass(ClassName
                                        .FOOTER_LG_FIXED) || $('body').hasClass(ClassName
                                        .FOOTER_XL_FIXED)) {
                                    if ($(Selector.FOOTER).css("position") === "fixed") {
                                        sidebarHeight = heights.window - heights.header - heights.footer;
                                    }
                                }

                                $(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css(
                                    'height', sidebarHeight);

                                if (typeof $.fn.overlayScrollbars !== 'undefined') {
                                    $(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT)
                                        .overlayScrollbars({
                                            className: this._config.scrollbarTheme,
                                            sizeAutoCapable: true,
                                            scrollbars: {
                                                autoHide: this._config.scrollbarAutoHide,
                                                clickScrolling: true
                                            }
                                        });
                                }
                            }
                        } // Static
                        ;

                        ControlSidebar._jQueryInterface = function _jQueryInterface(operation) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                if (!data) {
                                    data = new ControlSidebar(this, $(this).data());
                                    $(this).data(DATA_KEY, data);
                                }

                                if (data[operation] === 'undefined') {
                                    throw new Error(operation + " is not a function");
                                }

                                data[operation]();
                            });
                        };

                        return ControlSidebar;
                    }();
                /**
                 *
                 * Data Api implementation
                 * ====================================================
                 */


                $(document).on('click', Selector.DATA_TOGGLE, function(event) {
                    event.preventDefault();

                    ControlSidebar._jQueryInterface.call($(this), 'toggle');
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = ControlSidebar._jQueryInterface;
                $.fn[NAME].Constructor = ControlSidebar;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return ControlSidebar._jQueryInterface;
                };

                return ControlSidebar;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE Layout.js
             * License MIT
             * --------------------------------------------
             */
            var Layout = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'Layout';
                var DATA_KEY = 'lte.layout';
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Selector = {
                    HEADER: '.main-header',
                    MAIN_SIDEBAR: '.main-sidebar',
                    SIDEBAR: '.main-sidebar .sidebar',
                    CONTENT: '.content-wrapper',
                    BRAND: '.brand-link',
                    CONTENT_HEADER: '.content-header',
                    WRAPPER: '.wrapper',
                    CONTROL_SIDEBAR: '.control-sidebar',
                    LAYOUT_FIXED: '.layout-fixed',
                    FOOTER: '.main-footer',
                    PUSHMENU_BTN: '[data-widget="pushmenu"]',
                    LOGIN_BOX: '.login-box',
                    REGISTER_BOX: '.register-box'
                };
                var ClassName = {
                    HOLD: 'hold-transition',
                    SIDEBAR: 'main-sidebar',
                    CONTENT_FIXED: 'content-fixed',
                    SIDEBAR_FOCUSED: 'sidebar-focused',
                    LAYOUT_FIXED: 'layout-fixed',
                    NAVBAR_FIXED: 'layout-navbar-fixed',
                    FOOTER_FIXED: 'layout-footer-fixed',
                    LOGIN_PAGE: 'login-page',
                    REGISTER_PAGE: 'register-page'
                };
                var Default = {
                    scrollbarTheme: 'os-theme-light',
                    scrollbarAutoHide: 'l'
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var Layout =
                    /*#__PURE__*/
                    function() {
                        function Layout(element, config) {
                            this._config = config;
                            this._element = element;

                            this._init();
                        } // Public


                        var _proto = Layout.prototype;

                        _proto.fixLayoutHeight = function fixLayoutHeight() {
                            var heights = {
                                window: $(window).height(),
                                header: $(Selector.HEADER).length !== 0 ? $(Selector.HEADER)
                                    .outerHeight() : 0,
                                footer: $(Selector.FOOTER).length !== 0 ? $(Selector.FOOTER)
                                    .outerHeight() : 0,
                                sidebar: $(Selector.SIDEBAR).length !== 0 ? $(Selector.SIDEBAR)
                                    .height() : 0
                            };

                            var max = this._max(heights);

                            if (max == heights.window) {
                                $(Selector.CONTENT).css('min-height', max - heights.header - heights
                                    .footer);
                            } else {
                                $(Selector.CONTENT).css('min-height', max - heights.header);
                            }

                            if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
                                $(Selector.CONTENT).css('min-height', max - heights.header - heights
                                    .footer);

                                if (typeof $.fn.overlayScrollbars !== 'undefined') {
                                    $(Selector.SIDEBAR).overlayScrollbars({
                                        className: this._config.scrollbarTheme,
                                        sizeAutoCapable: true,
                                        scrollbars: {
                                            autoHide: this._config.scrollbarAutoHide,
                                            clickScrolling: true
                                        }
                                    });
                                }
                            }
                        } // Private
                        ;

                        _proto._init = function _init() {
                            var _this = this;

                            // Activate layout height watcher
                            this.fixLayoutHeight();
                            $(Selector.SIDEBAR).on('collapsed.lte.treeview expanded.lte.treeview',
                                function() {
                                    _this.fixLayoutHeight();
                                });
                            $(Selector.PUSHMENU_BTN).on('collapsed.lte.pushmenu shown.lte.pushmenu',
                                function() {
                                    _this.fixLayoutHeight();
                                });
                            $(window).resize(function() {
                                _this.fixLayoutHeight();
                            });

                            if (!$('body').hasClass(ClassName.LOGIN_PAGE) && !$('body').hasClass(ClassName
                                    .REGISTER_PAGE)) {
                                $('body, html').css('height', 'auto');
                            } else if ($('body').hasClass(ClassName.LOGIN_PAGE) || $('body').hasClass(
                                    ClassName.REGISTER_PAGE)) {
                                var box_height = $(Selector.LOGIN_BOX + ', ' + Selector.REGISTER_BOX)
                                    .height();
                                $('body').css('min-height', box_height);
                            }

                            $('body.hold-transition').removeClass('hold-transition');
                        };

                        _proto._max = function _max(numbers) {
                            // Calculate the maximum number in a list
                            var max = 0;
                            Object.keys(numbers).forEach(function(key) {
                                if (numbers[key] > max) {
                                    max = numbers[key];
                                }
                            });
                            return max;
                        } // Static
                        ;

                        Layout._jQueryInterface = function _jQueryInterface(config) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                var _config = $.extend({}, Default, $(this).data());

                                if (!data) {
                                    data = new Layout($(this), _config);
                                    $(this).data(DATA_KEY, data);
                                }

                                if (config === 'init') {
                                    data[config]();
                                }
                            });
                        };

                        return Layout;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(window).on('load', function() {
                    Layout._jQueryInterface.call($('body'));
                });
                $(Selector.SIDEBAR + ' a').on('focusin', function() {
                    $(Selector.MAIN_SIDEBAR).addClass(ClassName.SIDEBAR_FOCUSED);
                });
                $(Selector.SIDEBAR + ' a').on('focusout', function() {
                    $(Selector.MAIN_SIDEBAR).removeClass(ClassName.SIDEBAR_FOCUSED);
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = Layout._jQueryInterface;
                $.fn[NAME].Constructor = Layout;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return Layout._jQueryInterface;
                };

                return Layout;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE PushMenu.js
             * License MIT
             * --------------------------------------------
             */
            var PushMenu = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'PushMenu';
                var DATA_KEY = 'lte.pushmenu';
                var EVENT_KEY = "." + DATA_KEY;
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    COLLAPSED: "collapsed" + EVENT_KEY,
                    SHOWN: "shown" + EVENT_KEY
                };
                var Default = {
                    autoCollapseSize: 992,
                    enableRemember: false,
                    noTransitionAfterReload: true
                };
                var Selector = {
                    TOGGLE_BUTTON: '[data-widget="pushmenu"]',
                    SIDEBAR_MINI: '.sidebar-mini',
                    SIDEBAR_COLLAPSED: '.sidebar-collapse',
                    BODY: 'body',
                    OVERLAY: '#sidebar-overlay',
                    WRAPPER: '.wrapper'
                };
                var ClassName = {
                    SIDEBAR_OPEN: 'sidebar-open',
                    COLLAPSED: 'sidebar-collapse',
                    OPEN: 'sidebar-open'
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var PushMenu =
                    /*#__PURE__*/
                    function() {
                        function PushMenu(element, options) {
                            this._element = element;
                            this._options = $.extend({}, Default, options);

                            if (!$(Selector.OVERLAY).length) {
                                this._addOverlay();
                            }

                            this._init();
                        } // Public


                        var _proto = PushMenu.prototype;

                        _proto.show = function show() {
                            if (this._options.autoCollapseSize) {
                                if ($(window).width() <= this._options.autoCollapseSize) {
                                    $(Selector.BODY).addClass(ClassName.OPEN);
                                }
                            }

                            $(Selector.BODY).removeClass(ClassName.COLLAPSED);

                            if (this._options.enableRemember) {
                                localStorage.setItem("remember" + EVENT_KEY, ClassName.OPEN);
                            }

                            var shownEvent = $.Event(Event.SHOWN);
                            $(this._element).trigger(shownEvent);
                        };

                        _proto.collapse = function collapse() {
                            if (this._options.autoCollapseSize) {
                                if ($(window).width() <= this._options.autoCollapseSize) {
                                    $(Selector.BODY).removeClass(ClassName.OPEN);
                                }
                            }

                            $(Selector.BODY).addClass(ClassName.COLLAPSED);

                            if (this._options.enableRemember) {
                                localStorage.setItem("remember" + EVENT_KEY, ClassName.COLLAPSED);
                            }

                            var collapsedEvent = $.Event(Event.COLLAPSED);
                            $(this._element).trigger(collapsedEvent);
                        };

                        _proto.toggle = function toggle() {
                            if (!$(Selector.BODY).hasClass(ClassName.COLLAPSED)) {
                                this.collapse();
                            } else {
                                this.show();
                            }
                        };

                        _proto.autoCollapse = function autoCollapse(resize) {
                            if (resize === void 0) {
                                resize = false;
                            }

                            if (this._options.autoCollapseSize) {
                                if ($(window).width() <= this._options.autoCollapseSize) {
                                    if (!$(Selector.BODY).hasClass(ClassName.OPEN)) {
                                        this.collapse();
                                    }
                                } else if (resize == true) {
                                    if ($(Selector.BODY).hasClass(ClassName.OPEN)) {
                                        $(Selector.BODY).removeClass(ClassName.OPEN);
                                    }
                                }
                            }
                        };

                        _proto.remember = function remember() {
                            if (this._options.enableRemember) {
                                var toggleState = localStorage.getItem("remember" + EVENT_KEY);

                                if (toggleState == ClassName.COLLAPSED) {
                                    if (this._options.noTransitionAfterReload) {
                                        $("body").addClass('hold-transition').addClass(ClassName.COLLAPSED)
                                            .delay(50).queue(function() {
                                                $(this).removeClass('hold-transition');
                                                $(this).dequeue();
                                            });
                                    } else {
                                        $("body").addClass(ClassName.COLLAPSED);
                                    }
                                } else {
                                    if (this._options.noTransitionAfterReload) {
                                        $("body").addClass('hold-transition').removeClass(ClassName
                                            .COLLAPSED).delay(50).queue(function() {
                                            $(this).removeClass('hold-transition');
                                            $(this).dequeue();
                                        });
                                    } else {
                                        $("body").removeClass(ClassName.COLLAPSED);
                                    }
                                }
                            }
                        } // Private
                        ;

                        _proto._init = function _init() {
                            var _this = this;

                            this.remember();
                            this.autoCollapse();
                            $(window).resize(function() {
                                _this.autoCollapse(true);
                            });
                        };

                        _proto._addOverlay = function _addOverlay() {
                            var _this2 = this;

                            var overlay = $('<div />', {
                                id: 'sidebar-overlay'
                            });
                            overlay.on('click', function() {
                                _this2.collapse();
                            });
                            $(Selector.WRAPPER).append(overlay);
                        } // Static
                        ;

                        PushMenu._jQueryInterface = function _jQueryInterface(operation) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                var _options = $.extend({}, Default, $(this).data());

                                if (!data) {
                                    data = new PushMenu(this, _options);
                                    $(this).data(DATA_KEY, data);
                                }

                                if (operation === 'toggle') {
                                    data[operation]();
                                }
                            });
                        };

                        return PushMenu;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(document).on('click', Selector.TOGGLE_BUTTON, function(event) {
                    event.preventDefault();
                    var button = event.currentTarget;

                    if ($(button).data('widget') !== 'pushmenu') {
                        button = $(button).closest(Selector.TOGGLE_BUTTON);
                    }

                    PushMenu._jQueryInterface.call($(button), 'toggle');
                });
                $(window).on('load', function() {
                    PushMenu._jQueryInterface.call($(Selector.TOGGLE_BUTTON));
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = PushMenu._jQueryInterface;
                $.fn[NAME].Constructor = PushMenu;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return PushMenu._jQueryInterface;
                };

                return PushMenu;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE Treeview.js
             * License MIT
             * --------------------------------------------
             */
            var Treeview = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'Treeview';
                var DATA_KEY = 'lte.treeview';
                var EVENT_KEY = "." + DATA_KEY;
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    SELECTED: "selected" + EVENT_KEY,
                    EXPANDED: "expanded" + EVENT_KEY,
                    COLLAPSED: "collapsed" + EVENT_KEY,
                    LOAD_DATA_API: "load" + EVENT_KEY
                };
                var Selector = {
                    LI: '.nav-item',
                    LINK: '.nav-link',
                    TREEVIEW_MENU: '.nav-treeview',
                    OPEN: '.menu-open',
                    DATA_WIDGET: '[data-widget="treeview"]'
                };
                var ClassName = {
                    LI: 'nav-item',
                    LINK: 'nav-link',
                    TREEVIEW_MENU: 'nav-treeview',
                    OPEN: 'menu-open'
                };
                var Default = {
                    trigger: Selector.DATA_WIDGET + " " + Selector.LINK,
                    animationSpeed: 300,
                    accordion: true
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var Treeview =
                    /*#__PURE__*/
                    function() {
                        function Treeview(element, config) {
                            this._config = config;
                            this._element = element;
                        } // Public


                        var _proto = Treeview.prototype;

                        _proto.init = function init() {
                            this._setupListeners();
                        };

                        _proto.expand = function expand(treeviewMenu, parentLi) {
                            var _this = this;

                            var expandedEvent = $.Event(Event.EXPANDED);

                            if (this._config.accordion) {
                                var openMenuLi = parentLi.siblings(Selector.OPEN).first();
                                var openTreeview = openMenuLi.find(Selector.TREEVIEW_MENU).first();
                                this.collapse(openTreeview, openMenuLi);
                            }

                            treeviewMenu.stop().slideDown(this._config.animationSpeed, function() {
                                parentLi.addClass(ClassName.OPEN);
                                $(_this._element).trigger(expandedEvent);
                            });
                        };

                        _proto.collapse = function collapse(treeviewMenu, parentLi) {
                            var _this2 = this;

                            var collapsedEvent = $.Event(Event.COLLAPSED);
                            treeviewMenu.stop().slideUp(this._config.animationSpeed, function() {
                                parentLi.removeClass(ClassName.OPEN);
                                $(_this2._element).trigger(collapsedEvent);
                                treeviewMenu.find(Selector.OPEN + " > " + Selector.TREEVIEW_MENU)
                                    .slideUp();
                                treeviewMenu.find(Selector.OPEN).removeClass(ClassName.OPEN);
                            });
                        };

                        _proto.toggle = function toggle(event) {
                            var $relativeTarget = $(event.currentTarget);
                            var $parent = $relativeTarget.parent();
                            var treeviewMenu = $parent.find('> ' + Selector.TREEVIEW_MENU);

                            if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
                                if (!$parent.is(Selector.LI)) {
                                    treeviewMenu = $parent.parent().find('> ' + Selector.TREEVIEW_MENU);
                                }

                                if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
                                    return;
                                }
                            }

                            event.preventDefault();
                            var parentLi = $relativeTarget.parents(Selector.LI).first();
                            var isOpen = parentLi.hasClass(ClassName.OPEN);

                            if (isOpen) {
                                this.collapse($(treeviewMenu), parentLi);
                            } else {
                                this.expand($(treeviewMenu), parentLi);
                            }
                        } // Private
                        ;

                        _proto._setupListeners = function _setupListeners() {
                            var _this3 = this;

                            $(document).on('click', this._config.trigger, function(event) {
                                _this3.toggle(event);
                            });
                        } // Static
                        ;

                        Treeview._jQueryInterface = function _jQueryInterface(config) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                var _config = $.extend({}, Default, $(this).data());

                                if (!data) {
                                    data = new Treeview($(this), _config);
                                    $(this).data(DATA_KEY, data);
                                }

                                if (config === 'init') {
                                    data[config]();
                                }
                            });
                        };

                        return Treeview;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(window).on(Event.LOAD_DATA_API, function() {
                    $(Selector.DATA_WIDGET).each(function() {
                        Treeview._jQueryInterface.call($(this), 'init');
                    });
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = Treeview._jQueryInterface;
                $.fn[NAME].Constructor = Treeview;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return Treeview._jQueryInterface;
                };

                return Treeview;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE DirectChat.js
             * License MIT
             * --------------------------------------------
             */
            var DirectChat = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'DirectChat';
                var DATA_KEY = 'lte.directchat';
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    TOGGLED: "toggled{EVENT_KEY}"
                };
                var Selector = {
                    DATA_TOGGLE: '[data-widget="chat-pane-toggle"]',
                    DIRECT_CHAT: '.direct-chat'
                };
                var ClassName = {
                    DIRECT_CHAT_OPEN: 'direct-chat-contacts-open'
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var DirectChat =
                    /*#__PURE__*/
                    function() {
                        function DirectChat(element, config) {
                            this._element = element;
                        }

                        var _proto = DirectChat.prototype;

                        _proto.toggle = function toggle() {
                            $(this._element).parents(Selector.DIRECT_CHAT).first().toggleClass(ClassName
                                .DIRECT_CHAT_OPEN);
                            var toggledEvent = $.Event(Event.TOGGLED);
                            $(this._element).trigger(toggledEvent);
                        } // Static
                        ;

                        DirectChat._jQueryInterface = function _jQueryInterface(config) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                if (!data) {
                                    data = new DirectChat($(this));
                                    $(this).data(DATA_KEY, data);
                                }

                                data[config]();
                            });
                        };

                        return DirectChat;
                    }();
                /**
                 *
                 * Data Api implementation
                 * ====================================================
                 */


                $(document).on('click', Selector.DATA_TOGGLE, function(event) {
                    if (event) event.preventDefault();

                    DirectChat._jQueryInterface.call($(this), 'toggle');
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = DirectChat._jQueryInterface;
                $.fn[NAME].Constructor = DirectChat;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return DirectChat._jQueryInterface;
                };

                return DirectChat;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE TodoList.js
             * License MIT
             * --------------------------------------------
             */
            var TodoList = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'TodoList';
                var DATA_KEY = 'lte.todolist';
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Selector = {
                    DATA_TOGGLE: '[data-widget="todo-list"]'
                };
                var ClassName = {
                    TODO_LIST_DONE: 'done'
                };
                var Default = {
                    onCheck: function onCheck(item) {
                        return item;
                    },
                    onUnCheck: function onUnCheck(item) {
                        return item;
                    }
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var TodoList =
                    /*#__PURE__*/
                    function() {
                        function TodoList(element, config) {
                            this._config = config;
                            this._element = element;

                            this._init();
                        } // Public


                        var _proto = TodoList.prototype;

                        _proto.toggle = function toggle(item) {
                            item.parents('li').toggleClass(ClassName.TODO_LIST_DONE);

                            if (!$(item).prop('checked')) {
                                this.unCheck($(item));
                                return;
                            }

                            this.check(item);
                        };

                        _proto.check = function check(item) {
                            this._config.onCheck.call(item);
                        };

                        _proto.unCheck = function unCheck(item) {
                            this._config.onUnCheck.call(item);
                        } // Private
                        ;

                        _proto._init = function _init() {
                            var that = this;
                            $(Selector.DATA_TOGGLE).find('input:checkbox:checked').parents('li')
                                .toggleClass(ClassName.TODO_LIST_DONE);
                            $(Selector.DATA_TOGGLE).on('change', 'input:checkbox', function(event) {
                                that.toggle($(event.target));
                            });
                        } // Static
                        ;

                        TodoList._jQueryInterface = function _jQueryInterface(config) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                var _config = $.extend({}, Default, $(this).data());

                                if (!data) {
                                    data = new TodoList($(this), _config);
                                    $(this).data(DATA_KEY, data);
                                }

                                if (config === 'init') {
                                    data[config]();
                                }
                            });
                        };

                        return TodoList;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(window).on('load', function() {
                    TodoList._jQueryInterface.call($(Selector.DATA_TOGGLE));
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = TodoList._jQueryInterface;
                $.fn[NAME].Constructor = TodoList;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return TodoList._jQueryInterface;
                };

                return TodoList;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE CardWidget.js
             * License MIT
             * --------------------------------------------
             */
            var CardWidget = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'CardWidget';
                var DATA_KEY = 'lte.cardwidget';
                var EVENT_KEY = "." + DATA_KEY;
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    EXPANDED: "expanded" + EVENT_KEY,
                    COLLAPSED: "collapsed" + EVENT_KEY,
                    MAXIMIZED: "maximized" + EVENT_KEY,
                    MINIMIZED: "minimized" + EVENT_KEY,
                    REMOVED: "removed" + EVENT_KEY
                };
                var ClassName = {
                    CARD: 'card',
                    COLLAPSED: 'collapsed-card',
                    WAS_COLLAPSED: 'was-collapsed',
                    MAXIMIZED: 'maximized-card'
                };
                var Selector = {
                    DATA_REMOVE: '[data-card-widget="remove"]',
                    DATA_COLLAPSE: '[data-card-widget="collapse"]',
                    DATA_MAXIMIZE: '[data-card-widget="maximize"]',
                    CARD: "." + ClassName.CARD,
                    CARD_HEADER: '.card-header',
                    CARD_BODY: '.card-body',
                    CARD_FOOTER: '.card-footer',
                    COLLAPSED: "." + ClassName.COLLAPSED
                };
                var Default = {
                    animationSpeed: 'normal',
                    collapseTrigger: Selector.DATA_COLLAPSE,
                    removeTrigger: Selector.DATA_REMOVE,
                    maximizeTrigger: Selector.DATA_MAXIMIZE,
                    collapseIcon: 'fa-minus',
                    expandIcon: 'fa-plus',
                    maximizeIcon: 'fa-expand',
                    minimizeIcon: 'fa-compress'
                };

                var CardWidget =
                    /*#__PURE__*/
                    function() {
                        function CardWidget(element, settings) {
                            this._element = element;
                            this._parent = element.parents(Selector.CARD).first();

                            if (element.hasClass(ClassName.CARD)) {
                                this._parent = element;
                            }

                            this._settings = $.extend({}, Default, settings);
                        }

                        var _proto = CardWidget.prototype;

                        _proto.collapse = function collapse() {
                            var _this = this;

                            this._parent.children(Selector.CARD_BODY + ", " + Selector.CARD_FOOTER).slideUp(
                                this._settings.animationSpeed,
                                function() {
                                    _this._parent.addClass(ClassName.COLLAPSED);
                                });

                            this._parent.find(this._settings.collapseTrigger + ' .' + this._settings
                                .collapseIcon).addClass(this._settings.expandIcon).removeClass(this
                                ._settings.collapseIcon);

                            var collapsed = $.Event(Event.COLLAPSED);

                            this._element.trigger(collapsed, this._parent);
                        };

                        _proto.expand = function expand() {
                            var _this2 = this;

                            this._parent.children(Selector.CARD_BODY + ", " + Selector.CARD_FOOTER)
                                .slideDown(this._settings.animationSpeed, function() {
                                    _this2._parent.removeClass(ClassName.COLLAPSED);
                                });

                            this._parent.find(this._settings.collapseTrigger + ' .' + this._settings
                                .expandIcon).addClass(this._settings.collapseIcon).removeClass(this
                                ._settings.expandIcon);

                            var expanded = $.Event(Event.EXPANDED);

                            this._element.trigger(expanded, this._parent);
                        };

                        _proto.remove = function remove() {
                            this._parent.slideUp();

                            var removed = $.Event(Event.REMOVED);

                            this._element.trigger(removed, this._parent);
                        };

                        _proto.toggle = function toggle() {
                            if (this._parent.hasClass(ClassName.COLLAPSED)) {
                                this.expand();
                                return;
                            }

                            this.collapse();
                        };

                        _proto.maximize = function maximize() {
                            this._parent.find(this._settings.maximizeTrigger + ' .' + this._settings
                                .maximizeIcon).addClass(this._settings.minimizeIcon).removeClass(this
                                ._settings.maximizeIcon);

                            this._parent.css({
                                'height': this._parent.height(),
                                'width': this._parent.width(),
                                'transition': 'all .15s'
                            }).delay(150).queue(function() {
                                $(this).addClass(ClassName.MAXIMIZED);
                                $('html').addClass(ClassName.MAXIMIZED);

                                if ($(this).hasClass(ClassName.COLLAPSED)) {
                                    $(this).addClass(ClassName.WAS_COLLAPSED);
                                }

                                $(this).dequeue();
                            });

                            var maximized = $.Event(Event.MAXIMIZED);

                            this._element.trigger(maximized, this._parent);
                        };

                        _proto.minimize = function minimize() {
                            this._parent.find(this._settings.maximizeTrigger + ' .' + this._settings
                                .minimizeIcon).addClass(this._settings.maximizeIcon).removeClass(this
                                ._settings.minimizeIcon);

                            this._parent.css('cssText', 'height:' + this._parent[0].style.height +
                                ' !important;' + 'width:' + this._parent[0].style.width +
                                ' !important; transition: all .15s;').delay(10).queue(function() {
                                $(this).removeClass(ClassName.MAXIMIZED);
                                $('html').removeClass(ClassName.MAXIMIZED);
                                $(this).css({
                                    'height': 'inherit',
                                    'width': 'inherit'
                                });

                                if ($(this).hasClass(ClassName.WAS_COLLAPSED)) {
                                    $(this).removeClass(ClassName.WAS_COLLAPSED);
                                }

                                $(this).dequeue();
                            });

                            var MINIMIZED = $.Event(Event.MINIMIZED);

                            this._element.trigger(MINIMIZED, this._parent);
                        };

                        _proto.toggleMaximize = function toggleMaximize() {
                            if (this._parent.hasClass(ClassName.MAXIMIZED)) {
                                this.minimize();
                                return;
                            }

                            this.maximize();
                        } // Private
                        ;

                        _proto._init = function _init(card) {
                            var _this3 = this;

                            this._parent = card;
                            $(this).find(this._settings.collapseTrigger).click(function() {
                                _this3.toggle();
                            });
                            $(this).find(this._settings.maximizeTrigger).click(function() {
                                _this3.toggleMaximize();
                            });
                            $(this).find(this._settings.removeTrigger).click(function() {
                                _this3.remove();
                            });
                        } // Static
                        ;

                        CardWidget._jQueryInterface = function _jQueryInterface(config) {
                            var data = $(this).data(DATA_KEY);

                            if (!data) {
                                data = new CardWidget($(this), data);
                                $(this).data(DATA_KEY, typeof config === 'string' ? data : config);
                            }

                            if (typeof config === 'string' && config.match(
                                    /collapse|expand|remove|toggle|maximize|minimize|toggleMaximize/)) {
                                data[config]();
                            } else if (typeof config === 'object') {
                                data._init($(this));
                            }
                        };

                        return CardWidget;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(document).on('click', Selector.DATA_COLLAPSE, function(event) {
                    if (event) {
                        event.preventDefault();
                    }

                    CardWidget._jQueryInterface.call($(this), 'toggle');
                });
                $(document).on('click', Selector.DATA_REMOVE, function(event) {
                    if (event) {
                        event.preventDefault();
                    }

                    CardWidget._jQueryInterface.call($(this), 'remove');
                });
                $(document).on('click', Selector.DATA_MAXIMIZE, function(event) {
                    if (event) {
                        event.preventDefault();
                    }

                    CardWidget._jQueryInterface.call($(this), 'toggleMaximize');
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = CardWidget._jQueryInterface;
                $.fn[NAME].Constructor = CardWidget;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return CardWidget._jQueryInterface;
                };

                return CardWidget;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE CardRefresh.js
             * License MIT
             * --------------------------------------------
             */
            var CardRefresh = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'CardRefresh';
                var DATA_KEY = 'lte.cardrefresh';
                var EVENT_KEY = "." + DATA_KEY;
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    LOADED: "loaded" + EVENT_KEY,
                    OVERLAY_ADDED: "overlay.added" + EVENT_KEY,
                    OVERLAY_REMOVED: "overlay.removed" + EVENT_KEY
                };
                var ClassName = {
                    CARD: 'card'
                };
                var Selector = {
                    CARD: "." + ClassName.CARD,
                    DATA_REFRESH: '[data-card-widget="card-refresh"]'
                };
                var Default = {
                    source: '',
                    sourceSelector: '',
                    params: {},
                    trigger: Selector.DATA_REFRESH,
                    content: '.card-body',
                    loadInContent: true,
                    loadOnInit: true,
                    responseType: '',
                    overlayTemplate: '<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>',
                    onLoadStart: function onLoadStart() {},
                    onLoadDone: function onLoadDone(response) {
                        return response;
                    }
                };

                var CardRefresh =
                    /*#__PURE__*/
                    function() {
                        function CardRefresh(element, settings) {
                            this._element = element;
                            this._parent = element.parents(Selector.CARD).first();
                            this._settings = $.extend({}, Default, settings);
                            this._overlay = $(this._settings.overlayTemplate);

                            if (element.hasClass(ClassName.CARD)) {
                                this._parent = element;
                            }

                            if (this._settings.source === '') {
                                throw new Error(
                                    'Source url was not defined. Please specify a url in your CardRefresh source option.'
                                );
                            }

                            this._init();

                            if (this._settings.loadOnInit) {
                                this.load();
                            }
                        }

                        var _proto = CardRefresh.prototype;

                        _proto.load = function load() {
                            this._addOverlay();

                            this._settings.onLoadStart.call($(this));

                            $.get(this._settings.source, this._settings.params, function(response) {
                                    if (this._settings.loadInContent) {
                                        if (this._settings.sourceSelector != '') {
                                            response = $(response).find(this._settings.sourceSelector)
                                                .html();
                                        }

                                        this._parent.find(this._settings.content).html(response);
                                    }

                                    this._settings.onLoadDone.call($(this), response);

                                    this._removeOverlay();
                                }.bind(this), this._settings.responseType !== '' && this._settings
                                .responseType);
                            var loadedEvent = $.Event(Event.LOADED);
                            $(this._element).trigger(loadedEvent);
                        };

                        _proto._addOverlay = function _addOverlay() {
                            this._parent.append(this._overlay);

                            var overlayAddedEvent = $.Event(Event.OVERLAY_ADDED);
                            $(this._element).trigger(overlayAddedEvent);
                        };

                        _proto._removeOverlay = function _removeOverlay() {
                            this._parent.find(this._overlay).remove();

                            var overlayRemovedEvent = $.Event(Event.OVERLAY_REMOVED);
                            $(this._element).trigger(overlayRemovedEvent);
                        };

                        // Private
                        _proto._init = function _init(card) {
                            var _this = this;

                            $(this).find(this._settings.trigger).on('click', function() {
                                _this.load();
                            });
                        } // Static
                        ;

                        CardRefresh._jQueryInterface = function _jQueryInterface(config) {
                            var data = $(this).data(DATA_KEY);
                            var options = $(this).data();

                            if (!data) {
                                data = new CardRefresh($(this), options);
                                $(this).data(DATA_KEY, typeof config === 'string' ? data : config);
                            }

                            if (typeof config === 'string' && config.match(/load/)) {
                                data[config]();
                            } else if (typeof config === 'object') {
                                data._init($(this));
                            }
                        };

                        return CardRefresh;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(document).on('click', Selector.DATA_REFRESH, function(event) {
                    if (event) {
                        event.preventDefault();
                    }

                    CardRefresh._jQueryInterface.call($(this), 'load');
                });
                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = CardRefresh._jQueryInterface;
                $.fn[NAME].Constructor = CardRefresh;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return CardRefresh._jQueryInterface;
                };

                return CardRefresh;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE Dropdown.js
             * License MIT
             * --------------------------------------------
             */
            var Dropdown = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'Dropdown';
                var DATA_KEY = 'lte.dropdown';
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Selector = {
                    DROPDOWN_MENU: 'ul.dropdown-menu',
                    DROPDOWN_TOGGLE: '[data-toggle="dropdown"]'
                };
                var Default = {};
                /**
                 * Class Definition
                 * ====================================================
                 */

                var Dropdown =
                    /*#__PURE__*/
                    function() {
                        function Dropdown(element, config) {
                            this._config = config;
                            this._element = element;
                        } // Public


                        var _proto = Dropdown.prototype;

                        _proto.toggleSubmenu = function toggleSubmenu() {
                            this._element.siblings().show().toggleClass("show");

                            if (!this._element.next().hasClass('show')) {
                                this._element.parents('.dropdown-menu').first().find('.show').removeClass(
                                    "show").hide();
                            }

                            this._element.parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown',
                                function(e) {
                                    $('.dropdown-submenu .show').removeClass("show").hide();
                                });
                        } // Static
                        ;

                        Dropdown._jQueryInterface = function _jQueryInterface(config) {
                            return this.each(function() {
                                var data = $(this).data(DATA_KEY);

                                var _config = $.extend({}, Default, $(this).data());

                                if (!data) {
                                    data = new Dropdown($(this), _config);
                                    $(this).data(DATA_KEY, data);
                                }

                                if (config === 'toggleSubmenu') {
                                    data[config]();
                                }
                            });
                        };

                        return Dropdown;
                    }();
                /**
                 * Data API
                 * ====================================================
                 */


                $(Selector.DROPDOWN_MENU + ' ' + Selector.DROPDOWN_TOGGLE).on("click", function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    Dropdown._jQueryInterface.call($(this), 'toggleSubmenu');
                }); // $(Selector.SIDEBAR + ' a').on('focusin', () => {
                //   $(Selector.MAIN_SIDEBAR).addClass(ClassName.SIDEBAR_FOCUSED);
                // })
                // $(Selector.SIDEBAR + ' a').on('focusout', () => {
                //   $(Selector.MAIN_SIDEBAR).removeClass(ClassName.SIDEBAR_FOCUSED);
                // })

                /**
                 * jQuery API
                 * ====================================================
                 */

                $.fn[NAME] = Dropdown._jQueryInterface;
                $.fn[NAME].Constructor = Dropdown;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return Dropdown._jQueryInterface;
                };

                return Dropdown;
            }(jQuery);

            /**
             * --------------------------------------------
             * AdminLTE Toasts.js
             * License MIT
             * --------------------------------------------
             */
            var Toasts = function($) {
                /**
                 * Constants
                 * ====================================================
                 */
                var NAME = 'Toasts';
                var DATA_KEY = 'lte.toasts';
                var EVENT_KEY = "." + DATA_KEY;
                var JQUERY_NO_CONFLICT = $.fn[NAME];
                var Event = {
                    INIT: "init" + EVENT_KEY,
                    CREATED: "created" + EVENT_KEY,
                    REMOVED: "removed" + EVENT_KEY
                };
                var Selector = {
                    BODY: 'toast-body',
                    CONTAINER_TOP_RIGHT: '#toastsContainerTopRight',
                    CONTAINER_TOP_LEFT: '#toastsContainerTopLeft',
                    CONTAINER_BOTTOM_RIGHT: '#toastsContainerBottomRight',
                    CONTAINER_BOTTOM_LEFT: '#toastsContainerBottomLeft'
                };
                var ClassName = {
                    TOP_RIGHT: 'toasts-top-right',
                    TOP_LEFT: 'toasts-top-left',
                    BOTTOM_RIGHT: 'toasts-bottom-right',
                    BOTTOM_LEFT: 'toasts-bottom-left',
                    FADE: 'fade'
                };
                var Position = {
                    TOP_RIGHT: 'topRight',
                    TOP_LEFT: 'topLeft',
                    BOTTOM_RIGHT: 'bottomRight',
                    BOTTOM_LEFT: 'bottomLeft'
                };
                var Default = {
                    position: Position.TOP_RIGHT,
                    fixed: true,
                    autohide: false,
                    autoremove: true,
                    delay: 1000,
                    fade: true,
                    icon: null,
                    image: null,
                    imageAlt: null,
                    imageHeight: '25px',
                    title: null,
                    subtitle: null,
                    close: true,
                    body: null,
                    class: null
                };
                /**
                 * Class Definition
                 * ====================================================
                 */

                var Toasts =
                    /*#__PURE__*/
                    function() {
                        function Toasts(element, config) {
                            this._config = config;

                            this._prepareContainer();

                            var initEvent = $.Event(Event.INIT);
                            $('body').trigger(initEvent);
                        } // Public


                        var _proto = Toasts.prototype;

                        _proto.create = function create() {
                            var toast = $(
                                '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true"/>'
                            );
                            toast.data('autohide', this._config.autohide);
                            toast.data('animation', this._config.fade);

                            if (this._config.class) {
                                toast.addClass(this._config.class);
                            }

                            if (this._config.delay && this._config.delay != 500) {
                                toast.data('delay', this._config.delay);
                            }

                            var toast_header = $('<div class="toast-header">');

                            if (this._config.image != null) {
                                var toast_image = $('<img />').addClass('rounded mr-2').attr('src', this
                                    ._config.image).attr('alt', this._config.imageAlt);

                                if (this._config.imageHeight != null) {
                                    toast_image.height(this._config.imageHeight).width('auto');
                                }

                                toast_header.append(toast_image);
                            }

                            if (this._config.icon != null) {
                                toast_header.append($('<i />').addClass('mr-2').addClass(this._config
                                    .icon));
                            }

                            if (this._config.title != null) {
                                toast_header.append($('<strong />').addClass('mr-auto').html(this._config
                                    .title));
                            }

                            if (this._config.subtitle != null) {
                                toast_header.append($('<small />').html(this._config.subtitle));
                            }

                            if (this._config.close == true) {
                                var toast_close = $('<button data-dismiss="toast" />').attr('type',
                                        'button').addClass('ml-2 mb-1 close').attr('aria-label', 'Close')
                                    .append('<span aria-hidden="true">&times;</span>');

                                if (this._config.title == null) {
                                    toast_close.toggleClass('ml-2 ml-auto');
                                }

                                toast_header.append(toast_close);
                            }

                            toast.append(toast_header);

                            if (this._config.body != null) {
                                toast.append($('<div class="toast-body" />').html(this._config.body));
                            }

                            $(this._getContainerId()).prepend(toast);
                            var createdEvent = $.Event(Event.CREATED);
                            $('body').trigger(createdEvent);
                            toast.toast('show');

                            if (this._config.autoremove) {
                                toast.on('hidden.bs.toast', function() {
                                    $(this).delay(200).remove();
                                    var removedEvent = $.Event(Event.REMOVED);
                                    $('body').trigger(removedEvent);
                                });
                            }
                        } // Static
                        ;

                        _proto._getContainerId = function _getContainerId() {
                            if (this._config.position == Position.TOP_RIGHT) {
                                return Selector.CONTAINER_TOP_RIGHT;
                            } else if (this._config.position == Position.TOP_LEFT) {
                                return Selector.CONTAINER_TOP_LEFT;
                            } else if (this._config.position == Position.BOTTOM_RIGHT) {
                                return Selector.CONTAINER_BOTTOM_RIGHT;
                            } else if (this._config.position == Position.BOTTOM_LEFT) {
                                return Selector.CONTAINER_BOTTOM_LEFT;
                            }
                        };

                        _proto._prepareContainer = function _prepareContainer() {
                            if ($(this._getContainerId()).length === 0) {
                                var container = $('<div />').attr('id', this._getContainerId().replace('#',
                                    ''));

                                if (this._config.position == Position.TOP_RIGHT) {
                                    container.addClass(ClassName.TOP_RIGHT);
                                } else if (this._config.position == Position.TOP_LEFT) {
                                    container.addClass(ClassName.TOP_LEFT);
                                } else if (this._config.position == Position.BOTTOM_RIGHT) {
                                    container.addClass(ClassName.BOTTOM_RIGHT);
                                } else if (this._config.position == Position.BOTTOM_LEFT) {
                                    container.addClass(ClassName.BOTTOM_LEFT);
                                }

                                $('body').append(container);
                            }

                            if (this._config.fixed) {
                                $(this._getContainerId()).addClass('fixed');
                            } else {
                                $(this._getContainerId()).removeClass('fixed');
                            }
                        } // Static
                        ;

                        Toasts._jQueryInterface = function _jQueryInterface(option, config) {
                            return this.each(function() {
                                var _config = $.extend({}, Default, config);

                                var toast = new Toasts($(this), _config);

                                if (option === 'create') {
                                    toast[option]();
                                }
                            });
                        };

                        return Toasts;
                    }();
                /**
                 * jQuery API
                 * ====================================================
                 */


                $.fn[NAME] = Toasts._jQueryInterface;
                $.fn[NAME].Constructor = Toasts;

                $.fn[NAME].noConflict = function() {
                    $.fn[NAME] = JQUERY_NO_CONFLICT;
                    return Toasts._jQueryInterface;
                };

                return Toasts;
            }(jQuery);

            exports.CardRefresh = CardRefresh;
            exports.CardWidget = CardWidget;
            exports.ControlSidebar = ControlSidebar;
            exports.DirectChat = DirectChat;
            exports.Dropdown = Dropdown;
            exports.Layout = Layout;
            exports.PushMenu = PushMenu;
            exports.Toasts = Toasts;
            exports.TodoList = TodoList;
            exports.Treeview = Treeview;

            Object.defineProperty(exports, '__esModule', {
                value: true
            });

        }));
        //# sourceMappingURL=adminlte.js.map
    </script>
    @yield('scripts')
    @stack('js')
    <livewire:scripts />
</body>

</html>
