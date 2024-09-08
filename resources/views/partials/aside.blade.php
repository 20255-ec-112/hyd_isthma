  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-purple elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('dashboard') }}" class="brand-link flex">
          <img src="{{ asset('assets/img/logos/logo.png') }}" alt="Laravel Logo" class="brand-image"
              style="filter: invert(100%)" style="opacity: .8">

      </a>
      <!-- Sidebar -->
      <div class="sidebar">
          <!-- SidebarSearch Form -->
          <div class="row py-2 px-1">
              <div class="col-lg-12">
                  <span class="text-white">Hi, {{ ucfirst(auth()->user()->name) }}</span>
              </div>
          </div>
          <div class="form-inline">
              <div class="input-group" data-widget="sidebar-search">
                  <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                      aria-label="Search">
                  <div class="input-group-append">
                      <button class="btn btn-sidebar">
                          <i class="fas fa-search fa-fw"></i>
                      </button>
                  </div>
              </div>
          </div>
          <!-- Sidebar Menu -->
          <nav class="mt-2">
              @php
                  $urlsegment = explode('.', request()->route()->getName());
                  $urlsegment = is_array($urlsegment) ? $urlsegment[0] : 'NA';
              @endphp
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  {{-- DASHBOARD   --}}
                  <li class="nav-item">
                      <a href="{{ route('dashboard') }}"
                          class="nav-link {{ request()->route()->getName() === 'dashboard' ? 'active' : '' }}">
                          <i class="nav-icon fas fa-th"></i>
                          <p>
                              Dashboard
                          </p>
                      </a>
                  </li>
                  {{-- MEMBERS --}}
                  @if (auth()->user()->can('Create Members') ||
                          auth()->user()->can('View Members') ||
                          auth()->user()->can('Edit Members') ||
                          auth()->user()->can('Delete Members') ||
                          auth()->user()->id == 1)
                      <li class="nav-item">
                          <a href="{{ route('members.index') }}"
                              class="nav-link {{ in_array(request()->route()->getName(), ['members.index', 'members.create', 'members.edit']) ? 'active' : '' }}">
                              <i class="nav-icon fas fa-users"></i>
                              <p>
                                  Members
                              </p>
                          </a>
                      </li>
                  @endif
                  {{-- REGISTRATIONS --}}
                  @if (auth()->user()->can('Create Registrations') ||
                          auth()->user()->can('View Registrations') ||
                          auth()->user()->can('Edit Registrations') ||
                          auth()->user()->can('Delete Registrations') ||
                          auth()->user()->id == 1)
                      <li class="nav-item">
                          <a href="{{ route('registrations.index') }}"
                              class="nav-link {{ in_array(request()->route()->getName(), ['registrations.index', 'registrations.show', 'registrations.edit']) ? 'active' : '' }}">
                              <i class="nav-icon fas fa-file-alt"></i>
                              <p>
                                  Registrations
                              </p>
                          </a>
                      </li>
                  @endif
                  {{-- PROGRAMS --}}
                  @if (auth()->user()->can('View SessionThemes') ||
                          auth()->user()->can('View programSpeakers') ||
                          auth()->user()->can('View Programs') ||
                          auth()->user()->id == 1)
                      <li
                          class="nav-item  {{ str_contains('sessiontheme programSpeakers programs', $urlsegment) ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link  {{ str_contains('sessiontheme programSpeakers programs', $urlsegment) ? 'active' : '' }}">
                              <i class="nav-icon fas fa-microphone-alt"></i>
                              <p>
                                  Programs
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              {{-- SESSION THEMES --}}
                              @if (auth()->user()->can('Create SessionThemes') ||
                                      auth()->user()->can('View SessionThemes') ||
                                      auth()->user()->can('Edit SessionThemes') ||
                                      auth()->user()->can('Delete SessionThemes') ||
                                      auth()->user()->id == 1)
                                  <li class="nav-item">
                                      <a href="{{ route('sessiontheme.index') }}"
                                          class="nav-link {{ in_array(request()->route()->getName(), ['sessiontheme.index', 'sessiontheme.edit', 'sessiontheme.create']) ? 'active' : '' }}">
                                          <i class="nav-icon fas fa-users"></i>
                                          <p>
                                              Session Themes
                                          </p>
                                      </a>
                                  </li>
                              @endif
                              {{-- SPEAKERS --}}
                              @if (auth()->user()->can('Create programSpeakers') ||
                                      auth()->user()->can('View programSpeakers') ||
                                      auth()->user()->can('Edit programSpeakers') ||
                                      auth()->user()->can('Delete programSpeakers') ||
                                      auth()->user()->id == 1)
                                  <li class="nav-item">
                                      <a href="{{ route('programSpeakers.index') }}"
                                          class="nav-link {{ in_array(request()->route()->getName(), ['programSpeakers.index', 'programSpeakers.edit', 'programSpeakers.create']) ? 'active' : '' }}">
                                          <i class="nav-icon fas fa-user"></i>
                                          <p>
                                              Speakers
                                          </p>
                                      </a>
                                  </li>
                              @endif
                              {{-- SPEAKERS --}}
                              @if (auth()->user()->can('Create Programs') ||
                                      auth()->user()->can('View Programs') ||
                                      auth()->user()->can('Edit Programs') ||
                                      auth()->user()->can('Delete Programs') ||
                                      auth()->user()->id == 1)
                                  <li class="nav-item">
                                      <a href="{{ route('programs.index') }}"
                                          class="nav-link {{ in_array(request()->route()->getName(), ['programs.index', 'programs.edit', 'programs.create']) ? 'active' : '' }}">
                                          <i class="nav-icon fas fa-user"></i>
                                          <p>
                                              Programs
                                          </p>
                                      </a>
                                  </li>
                              @endif
                          </ul>
                      </li>
                  @endif
                  {{-- REPORTS --}}
                  <li
                      class="nav-item  {{ in_array(request()->route()->getName(), ['family-details-report', 'payment-details-report', 'arrival-report', 'departure-report', 'common-data-report', 'purchase-data-report', 'sightseeing-report', 'global-report']) ? 'menu-open' : '' }}">
                      <a href="#"
                          class="nav-link {{ in_array(request()->route()->getName(), ['family-details-report', 'payment-details-report', 'arrival-report', 'departure-report', 'common-data-report', 'purchase-data-report', 'sightseeing-report', 'global-report']) ? 'active' : '' }}">
                          <i class="nav-icon fas fa-file"></i>
                          <p>
                              Reports
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          @if (auth()->user()->can('View GlobalReport') || auth()->user()->id == 1)
                              <li class="nav-item">
                                  <a href="{{ route('global-report') }}"
                                      class="nav-link {{ in_array(request()->route()->getName(), ['global-report']) ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-globe"></i>
                                      <p>
                                          Global Report
                                      </p>
                                  </a>
                              </li>
                          @endif
                          @if (auth()->user()->can('View FamilyDetailsReport') || auth()->user()->id == 1)
                              <li class="nav-item">
                                  <a href="{{ route('family-details-report') }}"
                                      class="nav-link {{ request()->route()->getName() === 'family-details-report' ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-users"></i>
                                      <p>
                                          Family Details Report
                                      </p>
                                  </a>
                              </li>
                          @endif
                          @if (auth()->user()->can('View PaymentDetailsReport') || auth()->user()->id == 1)
                              <li class="nav-item">
                                  <a href="{{ route('payment-details-report') }}"
                                      class="nav-link {{ request()->route()->getName() === 'payment-details-report' ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-rupee-sign"></i>
                                      <p>
                                          Payment Details Report
                                      </p>
                                  </a>
                              </li>
                          @endif
                          @if (auth()->user()->can('View TravelReport') || auth()->user()->id == 1)
                              <li
                                  class="nav-item  {{ in_array(request()->route()->getName(), ['arrival-report', 'departure-report']) ? 'menu-open' : '' }}">
                                  <a href="#"
                                      class="nav-link {{ in_array(request()->route()->getName(), ['arrival-report', 'departure-report']) ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-paper-plane"></i>
                                      <p>
                                          Travel Report
                                          <i class="fas fa-angle-left right"></i>
                                      </p>
                                  </a>
                                  <ul class="nav nav-treeview">
                                      <li class="nav-item">
                                          <a href="{{ route('arrival-report') }}"
                                              class="nav-link {{ request()->route()->getName() === 'arrival-report' ? 'active' : '' }}">
                                              <i class="nav-icon fas fa-plane-arrival"></i>
                                              <p>
                                                  Arrival
                                              </p>
                                          </a>
                                      </li>
                                      <li class="nav-item">
                                          <a href="{{ route('departure-report') }}"
                                              class="nav-link {{ request()->route()->getName() === 'departure-report' ? 'active' : '' }}">
                                              <i class="nav-icon fas fa-plane-departure"></i>
                                              <p>
                                                  Departure
                                              </p>
                                          </a>
                                      </li>
                                  </ul>
                              </li>
                          @endif
                          @if (auth()->user()->can('View CommonDataReport') || auth()->user()->id == 1)
                              <li class="nav-item">
                                  <a href="{{ route('common-data-report') }}"
                                      class="nav-link {{ request()->route()->getName() === 'common-data-report' ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-file"></i>
                                      <p>
                                          Common Details Report
                                      </p>
                                  </a>
                              </li>
                          @endif
                          @if (auth()->user()->can('View PurchaseDataReport') || auth()->user()->id == 1)
                              <li class="nav-item">
                                  <a href="{{ route('purchase-data-report') }}"
                                      class="nav-link {{ request()->route()->getName() === 'purchase-data-report' ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-shopping-basket"></i>
                                      <p>
                                          Purchase Details Report
                                      </p>
                                  </a>
                              </li>
                          @endif
                          @if (auth()->user()->can('View SightSeeingReport') || auth()->user()->id == 1)
                              <li class="nav-item">
                                  <a href="{{ route('sight-seeing-details-report') }}"
                                      class="nav-link {{ request()->route()->getName() === 'sight-seeing-details-report' ? 'active' : '' }}">
                                      <i class="nav-icon fas fa-camera"></i>
                                      <p>
                                          Sight Seeing Report
                                      </p>
                                  </a>
                              </li>
                          @endif
                      </ul>
                  </li>
                  {{-- SETTINGS --}}
                  @if (auth()->user()->can('View Notificaitons') ||
                          auth()->user()->can('View Permissions') ||
                          auth()->user()->can('View Users') ||
                          auth()->user()->id == 1)
                      <li
                          class="nav-item  {{ str_contains('user notifications permissions', $urlsegment) ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ str_contains('user notifications permissions', $urlsegment) ? 'active' : '' }}">
                              <i class="nav-icon fas fa-cog"></i>
                              <p>
                                  Settings
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              @if (auth()->user()->can('Create Users') ||
                                      auth()->user()->can('View Users') ||
                                      auth()->user()->can('Edit Users') ||
                                      auth()->user()->can('Delete Users') ||
                                      auth()->user()->id == 1)
                                  <li class="nav-item">
                                      <a href="{{ route('user.index') }}"
                                          class="nav-link {{ in_array(request()->route()->getName(), ['user.index', 'user.edit', 'user.create']) ? 'active' : '' }}">
                                          <i class="nav-icon fas fa-users"></i>
                                          <p>
                                              Users
                                          </p>
                                      </a>
                                  </li>
                              @endif
                              @if (auth()->user()->can('Create Notifications') ||
                                      auth()->user()->can('View Notifications') ||
                                      auth()->user()->can('Edit Notifications') ||
                                      auth()->user()->can('Delete Notifications') ||
                                      auth()->user()->id == 1)
                                  <li class="nav-item">
                                      <a href="{{ route('notifications.index') }}"
                                          class="nav-link {{ request()->route()->getName() === 'notifications.index' ? 'active' : '' }}">
                                          <i class="nav-icon fas fa-bell"></i>
                                          <p>
                                              Notifications
                                          </p>
                                      </a>
                                  </li>
                              @endif
                              @if (auth()->user()->can('Create Permissions') ||
                                      auth()->user()->can('View Permissions') ||
                                      auth()->user()->can('Edit Permissions') ||
                                      auth()->user()->can('Delete Permissions') ||
                                      auth()->user()->id == 1)
                                  <li class="nav-item">
                                      <a href="{{ route('permissions.index') }}"
                                          class="nav-link {{ in_array(request()->route()->getName(), ['permissions.index', 'permissions.edit', 'permissions.create']) ? 'active' : '' }}">
                                          <i class="nav-icon fas fa-ban"></i>
                                          <p>
                                              Permissions
                                          </p>
                                      </a>
                                  </li>
                              @endif
                          </ul>
                      </li>
                  @endif
              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>
