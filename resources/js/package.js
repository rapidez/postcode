import { useDebounceFn, useMemoize } from '@vueuse/core'
import { on } from 'Vendor/rapidez/core/resources/js/polyfills/emit.js'

const getAddress = useMemoize(async function (postcode, housenumber, addition) {
    return window.rapidezAPI('post', 'postcode', { postcode, housenumber, addition })
})

// Splits a housenumber value that may contain an embedded addition, e.g.
// "3T" or "3 T", into its numeric housenumber and addition parts. Used when
// there's no dedicated addition field (street_lines < 3) so users can still
// type the addition straight into the housenumber field.
function splitHouseNumber(value) {
    const match = String(value ?? '').trim().match(/^(\d+)\s*(.*)$/)

    return match ? { housenumber: match[1], addition: match[2].trim() } : { housenumber: String(value ?? '').trim(), addition: '' }
}

async function updateAddress(address, event) {
    if ((address?.country_id || address?.country_code) != 'NL') {
        return
    }

    let rawHousenumber = address?.housenumber || address.street[1]

    if (!address.postcode || !rawHousenumber) {
        return
    }

    let { housenumber, addition } = splitHouseNumber(rawHousenumber)
    // A dedicated addition field (street_lines >= 3) takes precedence over
    // whatever was parsed out of the housenumber field itself.
    addition = address.street[2] || addition

    let response = await getAddress(address.postcode, housenumber, addition)

    if (!response?.found || !response?.city || !response?.street) {
        address.city = ''
        address.street[0] = ''
        return
    }

    address.city = response.city
    address.street[0] = response.street
    event?.target?.parentElement?.dispatchEvent?.(new Event('change', { bubbles: true }))
}

on('postcode-change', useDebounceFn(updateAddress, 100), { autoremove: false })
