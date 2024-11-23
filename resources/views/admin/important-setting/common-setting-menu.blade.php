<div class="buttons">
    <a href="{!! route('admin.important-setting.links.index') !!}" class="btn btn-primary mt-2 {{$active == 'links' ? 'active' : ''}}">Links</a>
    <a href="{!! route('admin.important-setting.policy-pages.index') !!}" class="btn btn-primary mt-2 {{$active == 'cms_pages' ? 'active' : ''}}">Policy Pages</a>
    <a href="{!! route('admin.important-setting.advance.index') !!}" class="btn btn-primary mt-2 {{$active == 'advance' ? 'active' : ''}}">Advance</a>
</div>
