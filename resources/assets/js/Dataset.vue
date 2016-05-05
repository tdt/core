<template>
    <div class="panel dataset panel-default" v-on:click="visit">
        <div class="panel-body">
            <div class='icon'>
                    <i class='fa fa-lg {{icon}}'></i>
            </div>
            <div>
                <div class='row'>
                    <div class='col-md-5'>
                        <h4 class='dataset-title'>
                            <a href='/{{ dataset.identifier }}'>{{ dataset.identifier }}</a>
                        </h4>
                        <div class='note dataset-description'>
                            {{ dataset.description }}
                        </div>
                    </div>
                    <div class='col-md-7 text-right hidden-sm hidden-xs'>
                        <div class='row'>
                            <span class='note'>
                                {{ dataset.rights }}
                            </span>
                        </div>
                        <div class='row'>
                            <span class='label label-success' v-for="format in dataset.formats">
                                {{ format }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: ['dataset'],
    computed: {
        icon() {
            switch (this.dataset.source_type) {
                case 'CsvDefinition':
                case 'XlsDefinition':
                    return 'fa-table';
                case 'LdDefinition':
                case 'SparqlDefinition':
                    return 'fa-code-fork';
                case 'ShpDefinition':
                    return 'fa-map-marker';
                case 'XmlDefinition':
                    return 'fa-code';
            }
            return 'fa-file-text-o';
        }
    },
    methods: {
        visit() {
            window.location.href = '/' + this.dataset.identifier
        }
    }
}

</script>
