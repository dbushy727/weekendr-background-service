<template>
    <div>
        <button type="button" class="btn btn-primary btn-lg btn-block" v-on:click="sendEmail">Send Email</button>
    </div>
</template>

<script>
    export default {
        methods: {
            sendEmail() {
                axios
                    .post('/admin/send-email')
                    .then(response => (this.info = response));
            }
        }
    }
</script>
