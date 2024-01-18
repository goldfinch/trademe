<% if Items(10) %>
<div class="container">
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    <% loop Items(10) %>
    <div class="col">
      <div class="card">
        <img
          src="{$itemImage}"
          height="250"
          style="object-fit: cover"
          class="card-img-top"
          alt="$itemTitle"
        />
        <div class="card-body">
          <div>
            <b>Link:</b>
            <a href="$itemLink" rel="noreferrer noopener" target="_blank">$itemLink</a>
          </div>
          <div><b>Date:</b> $itemStartDate(Y-m-d H:i:s)</div>
          <div><b>Date ago:</b> $itemStartDateAgo</div>
          <div><b>Title:</b> $itemTitle</div>
          <div><b>Category:</b> $itemCategory</div>
        </div>
      </div>
    </div>
    <% end_loop %>
  </div>
</div>
<% else %>
<div class="container">
  <p>There are no Trademe items</p>
</div>
<% end_if %>
