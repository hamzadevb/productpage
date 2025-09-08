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
    addtocart(event) {
        const button = event.target
        const product = button.getAttribute('data-product-id')

        fetch(`/add-to-cart/${product}`, {
            method: 'POST',
        })
        .then(response => {
            if (!response.ok) {
                this.showErrorToast()

                return;
            }

            this.showSuccessToast()
        })
        .catch(error => {
            this.showErrorToast()
        });
    }

    showSuccessToast() {
        const toast = document.getElementById('add-to-cart-success')
        const toastBootstrap = Toast.getOrCreateInstance(toast)

        toastBootstrap.show()
    }

    showErrorToast() {
        const toast = document.getElementById('add-to-cart-error')
        const toastBootstrap = Toast.getOrCreateInstance(toast)

        toastBootstrap.show()
    }
}
