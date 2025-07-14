document.addEventListener("DOMContentLoaded", () => {
  // Mapping des pays vers leurs devises
  const countryCurrencies = {
    CM: "XAF", // Cameroun
    CI: "XOF", // Côte d'Ivoire
    SN: "XOF", // Sénégal
    ML: "XOF", // Mali
    BF: "XOF", // Burkina Faso
    NE: "XOF", // Niger
    TG: "XOF", // Togo
    BJ: "XOF", // Bénin
    GN: "GNF", // Guinée
    FR: "EUR", // France
    US: "USD", // États-Unis
    CA: "CAD", // Canada
    GB: "GBP", // Royaume-Uni
  }

  // Fonction pour mettre à jour la devise
  function updateCurrency(countrySelect, currencySelect) {
    const selectedOption = countrySelect.options[countrySelect.selectedIndex]
    const countryCode = selectedOption.getAttribute("data-code")

    if (countryCode && countryCurrencies[countryCode]) {
      const currency = countryCurrencies[countryCode]

      // Mettre à jour le select de devise
      for (let i = 0; i < currencySelect.options.length; i++) {
        if (currencySelect.options[i].value === currency) {
          currencySelect.selectedIndex = i
          break
        }
      }

      // Déclencher l'événement change pour les listeners
      currencySelect.dispatchEvent(new Event("change"))
    }
  }

  // Appliquer à tous les formulaires avec pays/devise
  const countrySelects = document.querySelectorAll('select[name="country_id"]')

  countrySelects.forEach((countrySelect) => {
    const form = countrySelect.closest("form")
    const currencySelect = form ? form.querySelector('select[name="currency"]') : null

    if (currencySelect) {
      // Mettre à jour au chargement de la page
      updateCurrency(countrySelect, currencySelect)

      // Mettre à jour lors du changement de pays
      countrySelect.addEventListener("change", function () {
        updateCurrency(this, currencySelect)
      })
    }
  })
})
