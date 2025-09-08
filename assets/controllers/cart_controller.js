import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    static targets = [ "table", "total" ]

    connect() {
        this.setTotal()
    }

    removefromcart(event) {
        const button = event.target
        const product = button.getAttribute('data-product-id')
        const tr = button.parentElement.parentElement

        fetch(`/remove-from-cart/${product}`, {
            method: 'POST',
        })
        .then(response => {
            if (!response.ok) {
                this.showErrorToast()

                return;
            }

            this.showSuccessToast()

            tr.remove()

            this.setTotal()
        })
        .catch(error => {
            this.showErrorToast()
        });
    }

    showSuccessToast() {
        const toast = document.getElementById('remove-from-cart-success')
        const toastBootstrap = Toast.getOrCreateInstance(toast)

        toastBootstrap.show()
    }

    showErrorToast() {
        const toast = document.getElementById('remove-from-cart-error')
        const toastBootstrap = Toast.getOrCreateInstance(toast)

        toastBootstrap.show()
    }

    setTotal() {
        let total = 0.0;

        [...this.tableTarget.children].forEach((tr) => {
            const tds = [...tr.children]
            total += parseFloat(tds[2].textContent) * parseInt(tds[3].textContent)
        })


        this.totalTarget.textContent = total.toFixed(2)
    }
}
