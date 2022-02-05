
                <div class="app-sidebar sidebar-shadow">
                    <div class="app-header__logo">
                        <div class="logo-src"></div>
                        <div class="header__pane ml-auto">
                            <div>
                                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="app-header__mobile-menu">
                        <div>
                            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="app-header__menu">
                        <span>
                            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                                <span class="btn-icon-wrapper">
                                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                                </span>
                            </button>
                        </span>
                    </div>    



                    <div class="scrollbar-sidebar">
                        <div class="app-sidebar__inner">
                            <ul class="vertical-nav-menu">
                                
                                
                                <li class="app-sidebar__heading">
                                    <i class="metismenu-icon pe-7s-diamond"></i>
                                    <a href="{{url('home')}}" class="mm-active">Dashboard</a>
                                </li>


                                <li class="app-sidebar__heading">Appointments Management</li>
                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-cash"></i>
                                        Doctors & Patients
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('doctors')}}">
                                                <i class="metismenu-icon"></i>
                                                Doctor List
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{url('patients')}}">
                                                <i class="metismenu-icon"></i>
                                                Patient List
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{url('appointments')}}">
                                                <i class="metismenu-icon"></i>
                                                Appointmnet List
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{url('payments')}}">
                                                <i class="metismenu-icon"></i>
                                                Payment List
                                            </a>
                                        </li>
                                    </ul>
                                </li>   



                                 <li class="app-sidebar__heading">Locations Management</li>
                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-map-marker"></i>
                                        Location
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('countries')}}">
                                                <i class="metismenu-icon"></i>
                                                Country List
                                            </a>
                                        </li>
                                        
                                       

                                        <li>
                                            <a href="{{url('divisions')}}">
                                                <i class="metismenu-icon"></i>
                                                Division List
                                            </a>
                                        </li>
                                        
                                        

                                        <li>
                                            <a href="{{url('cities')}}">
                                                <i class="metismenu-icon"></i>
                                                City List
                                            </a>
                                        </li>
                                        
                                      

                                        <li>
                                            <a href="{{url('zones')}}">
                                                <i class="metismenu-icon"></i>
                                                Zone List
                                            </a>
                                        </li>
                                        
                                        

                                    </ul>
                                </li>

                                <li class="app-sidebar__heading">Users Management</li>
                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-add-user"></i>
                                        Users 
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('users')}}">
                                                <i class="metismenu-icon"></i>
                                                User List
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="{{url('users/create')}}">
                                                <i class="metismenu-icon">
                                                </i>Add User
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                               


                                <li class="app-sidebar__heading">Settings Management</li>

                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-study"></i>
                                          Advertisements 
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('advertisements')}}">
                                                <i class="metismenu-icon"></i>
                                                Advetisement List
                                            </a>
                                        </li>
                                        
                                        
                                    </ul>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-study"></i>
                                         Doctor Specialities 
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('specialities')}}">
                                                <i class="metismenu-icon"></i>
                                                Specialty List
                                            </a>
                                        </li>
                                        
                                        
                                    </ul>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-pin"></i>
                                       Appointment Setting
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('schedulings')}}">
                                                <i class="metismenu-icon"></i>
                                                Schedule List
                                            </a>
                                        </li>
                                     
                                        <li>
                                            <a href="{{url('appointment_charges')}}">
                                                <i class="metismenu-icon"></i>
                                                Charge List
                                            </a>
                                        </li>
                                        
                                        
                                    </ul>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-tools"></i>
                                        Site Configurations
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{url('settings')}}">
                                                <i class="metismenu-icon"></i>
                                                Setting List
                                            </a>
                                        </li>
                                        
                                        
                                    </ul>
                                </li>

                                
                            </ul>
                        </div>
                    </div>
                </div>    