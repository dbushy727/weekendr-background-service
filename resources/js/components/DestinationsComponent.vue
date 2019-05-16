<template>
    <div>
        <div class="destination" v-for="(destination, key) in destinations">
            <div class="name">{{ destination.name }}</div>
            <button class="btn btn-primary" v-on:click="getPendingImages(destination, key)">Get Images</button>
            <div class="images">
                <img
                    v-for="(image, imageKey) in destination.pendingImages"
                    v-bind:src="image.urls.small"
                    v-on:click="chooseImage(image, key, imageKey, destination.pendingImages)"
                    v-bind:class="{ selected: image.selected }">
            </div>
            <button class="btn btn-success" v-on:click="approveImage(destination, key)">Approve Image</button>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    img {

        width: 15em;

        &.selected { border: 5px solid blue; }
    }
</style>

<script>
    export default {
        data: function () {
            return {
                loading: false,
                destinations: []
            };
        },
        methods: {
            getDestinations() {
                this.loading = true;

                axios
                    .get('/destinations/pending')
                    .then((response) => {
                        this.destinations = response.data;
                        this.loading = false
                    });
            },
            getPendingImages(destination, key) {
                this.loading = true;

                axios
                    .get(`/destinations/${destination.name}/images`)
                    .then((response) => {
                        this.$set(this.destinations[key], 'pendingImages', response.data);
                        this.loading = false;
                    });
            },
            chooseImage(image, key, imageKey, pendingImages) {
                const that = this;

                _.each(pendingImages, function (img, k) {
                    that.$set(that.destinations[key].pendingImages[k], 'selected', false);
                });

                this.$set(this.destinations[key].pendingImages[imageKey], 'selected', true);
            },
            approveImage(destination, key) {
                axios
                    .put(`/destinations/${destination.name}`, {
                        image_url: _.first(_.filter(destination.pendingImages, {selected: true})).urls.regular
                    })
                    .then(() => {
                        console.log(_.filter(this.destinations, {name: destination.name}));
                        _.remove(this.destinations, {name: destination.name});
                    })

            }
        },
        mounted() {
            this.getDestinations();
        }
    }
</script>
