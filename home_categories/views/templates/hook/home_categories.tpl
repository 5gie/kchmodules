{if $home_categories}
<section id="home-categories">
  <div class="container">
    <div class="inner">
      <ul>
        {foreach from=$home_categories item=$category}
          <li>
            <a href="{$category.url}">
              <img src="{$category.image.bySize.category_default.url}" alt="{$category.name}">
              <h3>{$category.name}</h3>
            </a>
          </li>
        {/foreach}
      </ul>
    </div>
  </div>
</section>
{/if}