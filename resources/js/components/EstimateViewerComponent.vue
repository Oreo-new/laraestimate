<style>
    .fixed-buttons {
        position: fixed;
        top: 0;
        right: 0;
        z-index: 1030;
    }

    #estimateMainSection {
        min-height: 100vh;
        background-color: #eee;
    }

    #estimateMainSection h1 {
        text-align: left !important;
        font-size: 1.5rem;
    }

    #estimateMainSection tr.item:not(.selected) {
        color: #ccc;
        text-decoration: line-through;
    }

    #estimateMainSection input[type="checkbox"] {
        width: 1.5em;
        height: 1.5em;
    }

    .text-orange {
        color: #FB6C2A;
    }
    .footer-document{
        margin-top: 100px;
        padding-top: 10px;
    }
    .footer-document .col-4 span {
        font-size: 16px;
    }
    .footer-document .col-4 span a{
        color: black;
    }
    .footer-document .col-4 span a:hover{
        color: #1ABC9C;
        text-decoration: none;
    }
    .confirm{
        border: 0;
        color: #fff;
        background: #159a80;
        border-radius: 5px;
    }
    .confirm-by{
        width: 50%;
        margin-left: 10px;
    }
    .confirmed{
        margin: 0;
        font-style: italic;
        font-size: 14px;
        text-align: right;
    }
    .text-over {
        position: relative;
        padding-top: 5px;
        display: block;
    }
    .custom-border {
        width: 180px;
        position: absolute;
        border-top: 2px solid #000;
    }
    
    .noScreen{
        margin-top: 150px;
    }
    @media screen{
        .noPrint{}
        .noScreen{display:none;}
    }

    @media print {
        .noPrint{display:none;}
        .noScreen{}
    }
</style>

<template>
    <main id="estimateMainSection" class="p-md-5">

        <div class="modal fade" id="shareEstimateModal" v-if="estimateData">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ trans.get('app.share_estimate') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="link">{{ trans.get('app.copy_this_link') }}</label>
                        <input ref="shareableUrl" type="text" class="form-control" :value="estimateData.share_url"  @click="copyToClipboard">

                        <template v-if="canShareEmail">
                            <label for="link" class="mt-4">{{ trans.get('app.or_send_an_email') }}</label>
                            <input type="email" class="form-control" :placeholder="trans.get('app.type_email_address_here')" v-model="shareEmail">

                            <span class="mt-2 text-primary" v-show="sendingEmail">{{ trans.get('app.sending_email') }}</span>

                            <button class="btn btn-primary mt-4 float-right" :disabled="sendingEmail" @click="sendEmail()"><i class="icon ion-md-mail"></i> {{ trans.get('app.labels.send') }}</button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed-buttons p-4 text-right" v-if="estimateData">
            <button class="btn btn-primary d-print-none" @click="print()"><i class="icon ion-md-print"></i> {{ trans.get('app.labels.print') }}</button>
            <button class="btn btn-success d-print-none" @click="openShareModal()"><i class="icon ion-md-share"></i> {{ trans.get('app.labels.share') }}</button>
        </div>

        <div class="container" id="printContainer">

            <div class="row">

                <div id="estimateDocument" class="col-md-8 offset-md-2 bg-white p-md-5 animated bounceIn fast" v-if="estimateData">

                    <section class="m-4 text-center" v-if="estimateData.logo_image">
                        <img :src="estimateData.logo_image" alt="Estimate Image" width="150px">
                    </section>

                    <section class="mb-5" v-if="estimateData.use_name_as_title">
                        <h1><b>{{ estimateData.name }}</b></h1>
                    </section>
                    <p v-if="estimateData.hourly_rate != 0.00"><strong>Timepris:</strong> kr {{ estimateData.hourly_rate }} </p>
                    <p v-else class="noScreen"></p>
                    <section class="mb-5" v-for="section in estimateData.sections" :key="section.id">
                        <p v-html="section.presentable_text"></p>
                        
                        <div class="table-responsive">
                            <table class="table mt-4 p-sm-2 p-md-0" v-if="section.items.length">
                                <tr>
                                    <th></th>
                                    <th>{{ trans.get('app.description') }}</th>
                                    <th>{{ trans.get('app.duration') }}</th>
                                    <th class="text-right">{{ trans.get('app.price') }}</th>
                                </tr>

                                <tr v-for="item in section.items" :key="item.id" class="item" :class="{'selected': item.selected}">
                                    <td><input class="check-item" type="checkbox" v-if="!item.obligatory" v-model="item.selected" @change="renderPrices()"></td>
                                    <td>{{ item.description || '-' }}</td>
                                    <td>{{ item.duration || '-' }}</td>
                                    <td class="text-right">{{ formattedPrice(item.price) || '-' }}</td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="text-right"><b>{{ trans.get('app.section_total') }}:</b></td>
                                    <td class="text-right">{{ formattedPrice(sectionTotal(section)) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <div v-if="hasNonObligatory(section)">
                                <p><button class="confirm" @click="confirm(section)" >confirm</button></p>
                            </div>     
                        </div>
                        <div class="row">
                            <div class="col" style="text-align: right;">
                                <div v-if="!hasNonObligatory(section) && hasConfirmedby(section)">
                                    <p style="font-size: 16px; margin-top: 33px; font-style: italic;">Confirmed by: {{ getConfirmedBy(section) }} </p>
                                </div>
                            </div>
                        </div>
                    </section> 
                    <section class="noScreen">
                        <div class="row">
                           <div class="col-8">
                                <span class="custom-border"></span>
                                <span class="text-over">Kunde</span>
                            </div> 
                            <div class="col-4">
                                <span class="custom-border"></span>
                                <span class="text-over">Kloner AS</span>
                            </div> 
                        </div>
                    </section> 
                    <section class="footer-document">
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <span class="text-orange">Firma:</span> <span>Kloner AS</span>
                                <span class="text-orange">Kontaktperson:</span> <span>{{ userData.name }}</span>
                            </div>
                            <div class="col-4">
                                <span class="text-orange">Org. nr:</span> <span>996 706 354</span>
                                <span class="text-orange">E-post:</span> <span> <a :href="`mailto:${userData.email}`">{{ userData.email }}</a> </span>
                            </div>
                            <div class="col-4">
                                <span class="text-orange">Nettside:</span> <span> <a href="http://www.kloner.no">www.kloner.no</a> </span>
                                <span class="text-orange">Telefon:</span> <span>+47 22 65 30 08</span>    
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
export default {

    props: ['estimate', 'canShareEmail'],

    data() {
        return {
            shareEmail: '',
            sendingEmail: false,
            estimateData: null,
            userData: [],
            focusedd: false,
            checkItem: {},
            proof: false,
            userConfirm: [],
        }
    },

    created() {
        window.addEventListener('beforeprint', this.preparePrintMode);
        window.addEventListener('afterprint', this.returnToViewMode);
        Fire.$on('LoadEdit', () => {
            this.init();
        });
    },

    mounted() {
        this.init();
    },

    computed: {

        estimateTotalPrice() {
            if(!this.estimateData.sections) return 0;

            let total = this.estimateData.sections.reduce((sum, section) => {
                return sum + this.sectionTotal(section, false); 
            }, 0);

            return total;
        },

        estimateTotalSelectedPrice() {
            if(!this.estimateData.sections) return 0;
            
            let total = this.estimateData.sections.reduce((sum, section) => {
                return sum + this.sectionTotal(section, true); 
            }, 0);

            return total;
        },
        
        checkTrue() {
			return this.checkItem.filter(item => {
                if(item.obligatory > 1) {
                   
                    this.proof = true;
                    console.log(item);
                    return item;
                } else {
                    this.proof = true;
                    console.log(item);
                    return item;
                }
                
            });
        },

    },

    methods: {

        init() {
            axios.get('/estimates/' + this.estimate + '/data').then(({data}) => {
                this.estimateData = this.treatData(data);
                
                this.$nextTick(() => {
                    this.renderPrices();
                })
            });
            axios.get('/estimates/' + this.estimate + '/user').then(({data}) => {
                this.userData =  this.treatData1(data);
            });
            // axios.get('/userconfirmation/' + this.estimate).then(({data}) => {
            //     this.userData =  this.treatData2(data);
            // });

        },
        treatData(data) {
            data.sections = data.sections.map(section => {
                
                section.items = section.items.map(item => { 
                    item.selected = true ;
                    return item;
                });

                this.checkItem = section.items.map(item => { 
                    return item;
                });
                return section;
            });

            // return console.log(data);
            return data;
        },
        forConfirmation(){
            
        },
        treatData1(data) {
           return data;
        },
        confirm(section){
            console.log(section.id);
            let url = '/confirm/:item';
            url = url.replace(':item', section.id);
            axios.put(url, {estimate: this.estimate}).then(function (response) {
                console.log("success");
                Fire.$emit('LoadEdit');
            })["catch"](function (error) {
                console.log(error);
            });  
        },
        treatData2(data) {
            console.log(data);
           return data;
        },
        sectionTotal(section, onlySelected = true) {
            let total = section.items.reduce((sum, item) => {
                let itemPrice = (parseFloat(item.price) || 0);

                if(onlySelected) {
                    itemPrice = !item.selected ? 0 : itemPrice;
                }

                return sum + itemPrice; 
            }, 0);

            return parseFloat(total);
        },

        formattedPrice(price) {
            let currencySettings = this.estimateData.currency_settings;

            return currencySettings.symbol + ' ' + formatMoney(
                price, 2, 
                currencySettings.decimal_separator,
                currencySettings.thousands_separator,
            ).toString();
        },

        renderPrices() {
            let totalPriceElements = document.querySelectorAll('.total-calc-price');
            let totalSelectedPriceElements = document.querySelectorAll('.total-selected-calc-price');

            totalPriceElements.forEach(priceElement => {
                priceElement.innerHTML = this.formattedPrice(this.estimateTotalPrice);
            });

            totalSelectedPriceElements.forEach(priceElement => {
                priceElement.innerHTML = this.formattedPrice(this.estimateTotalSelectedPrice);
            });
        },

        openShareModal() {
            $('#shareEstimateModal').modal('show');
        },

        copyToClipboard() {
            var copyText = this.$refs.shareableUrl;

            copyText.select();
            copyText.setSelectionRange(0, 99999);

            document.execCommand('copy');

            toast.success('Link copied successfully');
        },

        sendEmail() {
            this.sendingEmail = true;

            axios.post('/estimates/' + this.estimate + '/share', {
                'email': this.shareEmail
            }).then(() => {
                this.sendingEmail = false;
                toast.success('E-mail sent successfully');
            }).catch(error => {
                this.sendingEmail = false;
                treatAxiosError(error);
            })
        },

        print() {
            window.print();
        },

        preparePrintMode() {
            /**
             * Wee need to manipulate the DOM here because VueJs can't
             * update the VirtualDOM on beforeprint event
             */
            let container = document.getElementById('printContainer'),
                estimate = document.getElementById('estimateDocument'),
                mainElement = document.getElementById('estimateMainSection');
            
            container.classList.remove('container');
            container.classList.add('container-fluid');
            estimate.classList.add('col');
            estimate.classList.remove('col-md-8');
            estimate.classList.remove('offset-md-2');

            mainElement.classList.add('bg-white');
        },

        returnToViewMode() {
            let container = document.getElementById('printContainer'),
                estimate = document.getElementById('estimateDocument'),
                mainElement = document.getElementById('estimateMainSection');
            
            container.classList.add('container');
            container.classList.remove('container-fluid');
            estimate.classList.remove('col');
            estimate.classList.add('col-md-8');
            estimate.classList.add('offset-md-2');

            mainElement.classList.remove('bg-white');
        },

        hasNonObligatory(section) {
            for (const item of section.items) {
                if(!item.obligatory) return true
            }
            return false
        },
        hasConfirmedby(section) {
            for (const item of section.items) {
                if(item.confirmed_by) return true
            }
            return false
        },

        getConfirmedBy(section) {
            for (const item of section.items) {
                return item.confirmed_by
            }
            return ''
        }

    }

}
</script>