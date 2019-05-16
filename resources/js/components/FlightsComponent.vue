<template>
    <div class="starter-template">
        <div class="text-center" v-if="loading">
            <div class="fa-3x">
              <i class="fas fa-cog fa-spin"></i>
            </div>
        </div>

        <b-tabs v-model="tabIndex" content-class="mt-3">
            <b-tab active>
                <template slot="title">
                    Pending ({{ pendingFlights.total}})
                </template>
                <div class="container">
                    <h2 v-if="pendingFlights.total == 0" class="text-center">
                        No Pending Flights
                    </h2>

                    <div v-if="pendingFlights.total > 0">
                        <br>
                        <h2 class="text-center">Pending Flight Deals ({{ pendingFlights.total }})</h2>
                        <br>
                        <div class="text-right">
                            <button class="btn btn-success" v-on:click="approveAll">Approve All</button>
                            <button class="btn btn-danger" v-on:click="rejectAll">Reject All</button>
                        </div>
                        <br>
                        <b-tabs content-class="mt-3">
                            <b-tab v-for="(flights, airport) in pendingFlights.airportData" :key="airport">
                                <template slot="title">{{ airport }} ({{flights.length}})</template>

                                <div class="row">

                                    <b-form-fieldset horizontal label="Filter" class="col-4">
                                        <b-form-input v-model="filter" placeholder="Type to Search"></b-form-input>
                                    </b-form-fieldset>

                                    <div class="col-4 offset-4" v-if="flights.length > perPage">
                                        <b-pagination size="md" :total-rows="flights.length" :per-page="perPage" v-model="currentPage" class="justify-content-end"/>
                                    </div>
                                </div>
                                <b-table
                                    striped
                                    hover
                                    :items="flights"
                                    :fields="fields"
                                    :current-page="currentPage"
                                    :per-page="perPage"
                                    :filter="filter"
                                    :sort-by.sync="sortBy"
                                    :sort-desc.sync="sortDesc">

                                    <template slot="price" scope="item">
                                        ${{ item.item.price / 100 }}
                                    </template>
                                    <template slot="dates" scope="item">
                                        {{ item.item.departure_date | moment("MMMM Do YYYY") }} - {{ item.item.return_date | moment("MMMM Do YYYY") }}
                                    </template>
                                    <template slot="destination_city" scope="item">
                                        {{ item.item.destination_city }} ({{ item.item.departure_destination }})
                                    </template>
                                    <template slot="actions" scope="item">
                                        <a target="_BLANK" :href="item.item.link"><button class="btn btn-warning"><i class="fas fa-eye"></i></button></a>
                                        <button class="btn btn-success" v-on:click="approve(item.item.id, $event)"><i class="fas fa-check"></i></button>
                                        <button class="btn btn-danger" v-on:click="reject(item.item.id, $event)"><i class="fas fa-times"></i></button>
                                    </template>
                                </b-table>

                                <div class="justify-content-center row my-1" v-if="flights.length > perPage">
                                    <b-pagination size="md" :total-rows="flights.length" :per-page="perPage" v-model="currentPage" />
                                </div>
                            </b-tab>
                        </b-tabs>

                    </div>

                </div>
            </b-tab>
            <b-tab>
                <template slot="title">
                    Approved ({{ approvedFlights.total}})
                </template>
                <div class="container">
                    <h2 v-if="approvedFlights.total == 0" class="text-center">
                        No Approved Flights
                    </h2>
                    <div v-if="approvedFlights.total > 0">
                        <br>
                        <h2 class="text-center">Approved Flight Deals ({{ approvedFlights.total }})</h2>
                        <br>
                        <div class="row">

                            <b-form-fieldset horizontal label="Filter" class="col-4" >
                                <b-form-input v-model="filter" placeholder="Type to Search"></b-form-input>
                            </b-form-fieldset>

                            <div class="col-4 offset-4" v-if="approvedFlights.data.length > perPage">
                                <b-pagination size="md" :total-rows="approvedFlights.data.length" :per-page="perPage" v-model="currentPage" class="justify-content-end"/>
                            </div>
                        </div>


                        <b-table
                            striped
                            hover
                            :items="approvedFlights.data"
                            :fields="fields"
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc">

                            <template slot="price" scope="item">
                                ${{ item.item.price / 100 }}
                            </template>
                            <template slot="dates" scope="item">
                                {{ item.item.departure_date | moment("MMMM Do YYYY") }} - {{ item.item.return_date | moment("MMMM Do YYYY") }}
                            </template>
                            <template slot="destination_city" scope="item">
                                {{ item.item.destination_city }} ({{ item.item.departure_destination }})
                            </template>
                            <template slot="actions" scope="item">
                                <a target="_BLANK" :href="item.item.link"><button class="btn btn-warning"><i class="fas fa-eye"></i></button></a>
                                <button class="btn btn-danger" v-on:click="reject(item.item.id, $event)"><i class="fas fa-times"></i></button>
                            </template>
                        </b-table>

                        <div class="justify-content-center row my-1" v-if="approvedFlights.data.length > perPage">
                            <b-pagination size="md" :total-rows="approvedFlights.data.length" :per-page="perPage" v-model="currentPage" />
                        </div>
                    </div>
                </div>
            </b-tab>
            <b-tab>
                <template slot="title">
                    Rejected ({{ rejectedFlights.total}})
                </template>
                <div class="container">
                    <h2 v-if="rejectedFlights.total == 0" class="text-center">
                        No Rejected Flights
                    </h2>
                    <div v-if="rejectedFlights.total > 0">
                        <br>
                        <h2 class="text-center">Rejected Flight Deals ({{ rejectedFlights.total }})</h2>
                        <br>

                        <div class="row">

                            <b-form-fieldset horizontal label="Filter" class="col-4" >
                                <b-form-input v-model="filter" placeholder="Type to Search"></b-form-input>
                            </b-form-fieldset>

                            <div class="col-4 offset-4" v-if="rejectedFlights.data.length > perPage">
                                <b-pagination size="md" :total-rows="rejectedFlights.data.length" :per-page="perPage" v-model="currentPage" class="justify-content-end" />
                            </div>

                        </div>

                        <b-table
                            striped
                            hover
                            :items="rejectedFlights.data"
                            :fields="fields"
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc">

                            <template slot="price" scope="item">
                                ${{ item.item.price / 100 }}
                            </template>
                            <template slot="dates" scope="item">
                                {{ item.item.departure_date | moment("MMMM Do YYYY") }} - {{ item.item.return_date | moment("MMMM Do YYYY") }}
                            </template>
                            <template slot="destination_city" scope="item">
                                {{ item.item.destination_city }} ({{ item.item.departure_destination }})
                            </template>
                            <template slot="actions" scope="item">
                                <a target="_BLANK" :href="item.item.link"><button class="btn btn-warning"><i class="fas fa-eye"></i></button></a>
                                <button class="btn btn-success" v-on:click="approve(item.item.id, $event)"><i class="fas fa-check"></i></button>
                            </template>
                        </b-table>

                        <div class="justify-content-center row my-1" v-if="rejectedFlights.data.length > perPage">
                            <b-pagination size="md" :total-rows="rejectedFlights.data.length" :per-page="perPage" v-model="currentPage" />
                        </div>
                    </div>
                </div>
            </b-tab>
        </b-tabs>

    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                pendingFlights: {},
                approvedFlights: {},
                rejectedFlights: {},
                loading: true,
                tabIndex: 0,
                currentPage: 1,
                perPage: 20,
                filter: null,
                sortBy: 'id',
                sortDesc: true,
                fields: {
                    id: {
                        key: 'id',
                        label: 'Flight ID',
                        sortable: true,
                    },
                    price: {
                        label: 'Price',
                        sortable: true,
                    },
                    destination_city: {
                        label: 'Destination',
                        sortable: true,
                    },
                    departure_origin: {
                        key: 'departure_origin',
                        label: 'Origin',
                        sortable: true,
                    },
                    carriers: {
                        key: 'carriers',
                        label: 'Carriers',
                        sortable: true,
                    },
                    dates: {
                        label: 'Dates',
                    },
                    actions: {
                        label: 'Actions',
                    },
                }
            };
        },
        watch: {
            tabIndex: function (val) {
                if (val == 0) {
                    this.fetchPending();
                } else {
                    this.fetchReady();
                }
            }
        },
        methods: {
            fetchPending() {
                this.loading = true

                axios
                    .get('/flight-deals/pending')
                    .then(response => {
                        this.pendingFlights = response.data;
                        this.pendingFlights.airportData = _.groupBy(response.data.data, 'departure_origin');
                        this.loading = false;
                    });
            },
            fetchReady() {
                this.loading = true;

                axios
                    .get('/flight-deals/ready')
                    .then(response => {this.approvedFlights = response.data; this.loading = false;})
            },
            fetchRejected() {
                this.loading = true;

                axios
                    .get('/flight-deals/rejected')
                    .then(response => {this.rejectedFlights = response.data; this.loading = false;})
            },
            approve(id, event) {
                axios
                    .post(`/flight-deals/${id}/approve`)
                    .then(this.fetchPending)
                    .then(this.fetchReady)
                    .then(this.fetchRejected);
            },
            reject(id, event) {
                axios
                    .post(`/flight-deals/${id}/reject`)
                    .then(this.fetchPending)
                    .then(this.fetchReady)
                    .then(this.fetchRejected);
            },
            approveAll() {
                const promises = _.map(this.pendingFlights.data, function (flight) {
                    return axios.post(`/flight-deals/${flight.id}/approve`);
                });

                Promise.all(promises)
                    .then(this.fetchPending)
                    .then(this.fetchReady)
                    .then(this.fetchRejected);
            },
            rejectAll() {
                const promises = _.map(this.pendingFlights.data, function (flight) {
                    return axios.post(`/flight-deals/${flight.id}/reject`);
                });

                Promise.all(promises)
                    .then(this.fetchPending)
                    .then(this.fetchReady)
                    .then(this.fetchRejected);
            }
        },
        mounted() {
            this.fetchPending();
            this.fetchReady();
            this.fetchRejected();
        }
    }
</script>
