<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="mdi mdi-account-box-multiple"></i>
                    <span class="nav-text"> BMI Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০2</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{route('admin.bmi-get-page')}}">
                            <i class="mdi mdi-human"></i>
                            <span class="nav-text">BMI Reference</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.bmi.test.recommend.page')}}">
                            <i class="mdi mdi-test-tube-empty"></i>
                            <span class="nav-text">BMI Test Recommendation</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="mega-menu mega-menu-lg">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="mdi mdi-account-box-multiple"></i>
                    <span class="nav-text"> SHA Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০4</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{route('admin.sha.page')}}">
                            <i class="mdi mdi-notebook"></i>
                            <span class="nav-text">Questions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.sha.test.recommend.page')}}">
                            <i class="mdi mdi-test-tube-off"></i>
                            <span class="nav-text">SHA Tests Recommendation</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.sha.task.recommend.page')}}">
                            <i class="mdi mdi-run-fast"></i>
                            <span class="nav-text">SHA Tasks Recommendation</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.sha.level.restriction.page')}}">
                            <i class="mdi mdi-account-alert"></i>
                            <span class="nav-text">Level Restrictions</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="mega-menu mega-menu-lg">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="mdi mdi-account-box-multiple"></i>
                    <span class="nav-text"> User Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০1</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{url('/admin/users-tasks/')}}">
                            <i class="mdi mdi-account"></i>
                            Users Tasks
                        </a>
                    </li>

                </ul>
            </li>
            {{--Blog--}}
            <li class="mega-menu mega-menu-lg">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="mdi mdi-blogger"></i><span class="nav-text">Blog Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০2</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{route('admin.blog.add_edit',['action' => 'add'])}}">
                            <i class="mdi mdi-plus-circle"></i>
                            Add Blog
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.blog.fetch')}}">
                            <i class="mdi mdi-eye"></i>
                            View Blogs
                        </a>
                    </li>
                </ul>
            </li>
            {{--Task--}}
            <li class="mega-menu mega-menu-lg">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="mdi mdi-run-fast"></i><span class="nav-text">Task Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০4</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{route('admin.regimen.page',['category' => 'physics'])}}"><i
                                    class="mdi mdi-heart-pulse"></i> Physical</a></li>
                    <li><a href="{{route('admin.regimen.page',['category' => 'mental'])}}"><i
                                    class="mdi mdi-face"></i> Mental Health</a></li>
                    <li><a href="{{route('admin.regimen.page',['category' => 'lifestyle'])}}"><i
                                    class="mdi mdi-nature-people"></i> Lifestyle</a></li>
                    <li><a href="{{route('admin.regimen.page',['category' => 'nutrition'])}}"><i
                                    class="mdi mdi-food-apple"></i> Nutrition</a></li>
                    <li>
                        <a href="{{route("admin.ntr_bank.page")}}">
                            <i class="mdi mdi-nutrition"></i>
                            Nutrition Recommendation
                        </a>
                    </li>
                    <li>
                        <a href="{{route("admin.mntl_bank.page")}}">
                            <i class="mdi mdi-read"></i>
                            Mental Bank Recommendation
                        </a>
                    </li>
                </ul>
            </li>
            {{--Assess--}}
            <li class="mega-menu mega-menu-lg">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="true">
                    <i class="mdi mdi-pencil-box"></i><span class="nav-text">Assess Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০5</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a class="has-arrow" href="javascript:void(0);" aria-expanded="true">
                            <i class="mdi mdi-pencil-box"></i><span class="nav-text">Recommendation</span>
                            <span class="badge bg-linkedin text-white nav-badge">০2</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{route("admin.assess.regimen.recommend.page")}}"><i
                                            class="mdi mdi-calendar-text"></i> Task Recommendation</a></li>
                            <li>
                                <a href="{{route("admin.assess.test.recommend.page")}}"><i class="mdi mdi-receipt"></i>
                                    Test Recommendation</a></li>
                            </li>
                        </ul>
                    <li>
                    <li>
                        <a class="has-arrow" href="javascript:void(0);" aria-expanded="true">
                            <i class="mdi mdi-pencil-box"></i><span class="nav-text">Category</span>
                            <span class="badge bg-linkedin text-white nav-badge">০2</span>
                        </a>
                        <ul aria-expanded="false">
                            <a href="{{url('/admin/assess/category')}}">
                                <i class="mdi mdi-comment-question-outline text-left"></i>
                                Category Logic
                            </a>
                            </li>
                            <li>
                                <a href="{{url('/admin/assess/tag')}}">
                                    <i class="mdi mdi-comment-question-outline"></i>
                                    Sub-Category Logic
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void(0);" aria-expanded="true">
                            <i class="mdi mdi-pencil-box"></i><span class="nav-text">Questions</span>
                            <span class="badge bg-linkedin text-white nav-badge">০3</span>
                        </a>
                        <ul aria-expanded="false">
                            <li>
                                <a href="{{url('/admin/assess/question')}}">
                                    <i class="mdi mdi-comment-question-outline"></i>
                                    Assessment Questions
                                </a>
                            </li>
                            <li>
                                <a href="{{route("admin.assess.tag.order")}}">
                                    <i class="mdi mdi-arrange-send-backward"></i>
                                    Questions Tag Order
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{url('/admin/assess/interpolation')}}">
                            <i class="mdi mdi-email-outline"></i>
                            Score interpolation for Physical
                        </a>
                    </li>
                    <li>
                        <a href="{{route("admin.assess.score-level-map.page")}}">
                            <i class="mdi mdi-map-plus"></i>
                            Mental Score Level Mapping
                        </a>
                    </li>
                </ul>
            </li>
            {{--Diagnostic Lab--}}
            <li class="mega-menu mega-menu-lg">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="mdi mdi-hospital"></i><span class="nav-text">Diagnostic Lab Module</span>
                    <span class="badge bg-linkedin text-white nav-badge">০2</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{url('/admin/assess/view/tests')}}">
                            <i class="mdi mdi-test-tube"></i>
                            Labs Tests
                        </a>
                    </li>
                    <li>
                        <a href="{{route("admin.mmg.mail.receivers")}}">
                            <i class="mdi mdi-human"></i>
                            Mail Receiver Members
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</div>