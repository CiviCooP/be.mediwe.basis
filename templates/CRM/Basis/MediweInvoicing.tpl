{literal}
  <script type="text/javascript">
    cj("th a").each(function () {
      if (this.innerHTML === 'Bedankje verzonden') {
        cj(this).parent().hide();
      }
    });
    cj(".crm-contribution-thankyou_date").hide();

  </script>
{/literal}