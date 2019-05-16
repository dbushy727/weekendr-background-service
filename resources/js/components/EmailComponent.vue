<template>
    <div>
        <br>
        <div class="text-center" v-if="loading">
            <div class="fa-3x">
              <i class="fas fa-cog fa-spin"></i>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-lg btn-block" v-on:click="sendEmail">Send Email</button>
    </div>
</template>

<script>
    export default {
        data: function () {
            return { loading: false };
        },
        methods: {
            sendEmail() {
                this.loading = true;

                axios
                    .post('/admin/send-email')
                    .then(() => window.location.reload(true));
            }
        }
    }
</script>
