<template>
  <!-- SweetAlert will be triggered via a method -->
</template>

<script setup>
import Swal from 'sweetalert2'
import { defineExpose } from 'vue'

const props = defineProps({
  onConfirm: {
    type: Function,
    required: true
  }
})

function show() {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'No, cancel!',
    customClass: {
      confirmButton: 'bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 me-4',
      cancelButton: 'bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 me-4'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      props.onConfirm()
      Swal.fire('Deleted!', 'Your document has been deleted.', 'success')
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      Swal.fire('Cancelled', 'Your document is safe', 'error')
    }
  })
}

defineExpose({ show })
</script>
