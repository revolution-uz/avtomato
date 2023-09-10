@php /*** @var \App\DTOs\Common\MenuItem[] $menu **/ @endphp
<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    {{-- active menu method --}}
    @php
      $activeClass = null;
      $active = $configData["layout"] === 'vertical' ? 'active open':'active';
      $currentRoute =  current_route();

      if ($currentRoute === $submenu->url) {
          $activeClass = 'active';
      }
      elseif (isset($submenu->subMenu)) {
        if (gettype($submenu->url) === 'array') {
          foreach($submenu->url as $slug){
            if (str_starts_with($currentRoute, $slug)) {
                $activeClass = $active;
            }
          }
        }
        else{
          if (str_starts_with($currentRoute, $submenu->url)) {
            $activeClass = $active;
          }
        }
      }
    @endphp

      <li class="menu-item {{$activeClass}}">
        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" class="{{ isset($submenu->subMenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
          @if (isset($submenu->icon))
          <i class="{{ $submenu->icon }}"></i>
          @endif
          <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
        </a>

        {{-- submenu --}}
        @if (isset($submenu->subMenu))
          @include('layouts.sections.menu.submenu',['menu' => $submenu->subMenu])
        @endif
      </li>
    @endforeach
  @endif
</ul>
