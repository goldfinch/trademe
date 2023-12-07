<% if Items($limit) %>
  <div class="container">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
      <% loop Items($limit) %>
        <%-- <div class="col"></div> --%>
      <% end_loop %>
    </div>
  </div>
<% else %>
  <div class="container">
    <p>There are no TradeMe items</p>
  </div>
<% end_if %>
