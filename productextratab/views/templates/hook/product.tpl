{if $extra_fields}
    <section id="extra_fields">
        <div class="inner">
            {foreach from=$extra_fields item=field key=key}
            <div class="col">
                <div class="box">
                    <div class="close">
                        <span class="material-icons">close</span>
                    </div>
                    <div class="open">
                        <span>+</span>
                        <h3>{$field.title nofilter}</h3>
                        <p>{$field.subtitle nofilter}</p>
                    </div>
                    <div class="text">
                        <div class="content">
                            {$field.content nofilter}
                        </div>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </section>
{/if}