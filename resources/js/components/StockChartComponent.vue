<script>
import { Line, mixins } from 'vue-chartjs';
const { reactiveProp } = mixins

export default {
  extends: Line,
  mixins: [reactiveProp],
  props: {
    label: {
      type: String
    },
    chartData: {
      type: Object
    },
    options: {
      type: Object
    }
  },

  mounted () {
    this.renderChart(this.chartData, this.options);
  }
}
</script>