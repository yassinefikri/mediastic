<template>
  <div ref="this-form" class="container">
    <my-form
        :getUrl="getUrl"
        :postUrl="postUrl"
        :message="message"
        :clearFormAfterSubmit="undefined === postId"
        @form-posted="formPosted"
    />
  </div>
</template>

<script>
import MyForm from "../Partials/MyForm"

export default {
  name: "post-form",
  components: {MyForm,},
  props: ['postId'],
  data(){
    return {
      getUrl: (undefined !== this.postId) ? this.$Routing.generate('post_edit_front', {'id': this.postId}) : this.$Routing.generate('post_add_front'),
      postUrl: (undefined !== this.postId) ? this.$Routing.generate('post_edit', {'id': this.postId}) : this.$Routing.generate('post_add'),
      message: (undefined !== this.postId) ? 'Your post has been updated' : 'Your post has been published'
    }
  },
  mounted() {
    this.$refs['this-form'].addEventListener('click', function (e) {
      if (e.target.classList.contains('add-another-collection-widget')) {
        let form = this.querySelector('form')
        let list = form.querySelector(e.target.getAttribute('data-list-selector'))
        // Try to find the counter of the list or use the length of the list
        let counter = parseInt(list.getAttribute('data-widget-counter') || list.children().length)
        // grab the prototype template
        let newWidget = list.getAttribute('data-prototype')
        newWidget = newWidget.replace(/__name__/g, counter)
        // Create new widget container
        const inputContainer = document.createElement('div')
        inputContainer.classList.add('d-inline-block', 'flex-grow-1')
        inputContainer.innerHTML = newWidget
        // Increase the counter
        list.setAttribute('data-widget-counter', ++counter)
        // Create wrapper
        const finalNode = document.createElement('div')
        finalNode.classList.add('mb-2', 'd-flex')
        finalNode.append(inputContainer)
        // Create delete button
        const deleteButton = document.createElement('button')
        deleteButton.classList.add('btn', 'btn-outline-danger', 'me-2')
        deleteButton.innerHTML = '<i class="bi-trash"></i>'
        deleteButton.addEventListener('click', (e) => {
          e.preventDefault()
          finalNode.remove()
        })
        // Create delete button container
        const deleteButtonContainer = document.createElement('div')
        deleteButtonContainer.append(deleteButton)
        finalNode.prepend(deleteButtonContainer)
        // add the new element to the list
        list.append(finalNode)
      }
    })
  },
  methods: {
    formPosted(){
      if(undefined ===this.postId) {
        let imagesList = this.$refs['this-form'].querySelector(this.$refs['this-form'].querySelector('.add-another-collection-widget').getAttribute('data-list-selector'))
        imagesList.setAttribute('data-widget-counter', 0)
        imagesList.innerHTML = ''
        this.$emit('new-post')
      } else {
        this.$router.push({name: 'post_view', params: {'id': this.postId}})
      }
    }
  }
}
</script>

<style scoped>

</style>