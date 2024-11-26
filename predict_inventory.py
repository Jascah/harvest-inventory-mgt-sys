import pandas as pd
from statsmodels.tsa.holtwinters import ExponentialSmoothing
import sys
import json

def predict_inventory(data, forecast_periods=7):
    try:
        # Convert input data into a DataFrame
        df = pd.DataFrame(data)
        df['date_added'] = pd.to_datetime(df['date_added'])
        df.set_index('date_added', inplace=True)

        # Resample to daily data, summing quantities
        daily_data = df.resample('D').sum()

        # Create and fit the model
        model = ExponentialSmoothing(
            daily_data['quantity'], 
            seasonal=None, 
            trend="add",
            damped_trend=True
        )
        fit = model.fit()

        # Forecast future inventory levels
        forecast = fit.forecast(steps=forecast_periods)

        # Convert forecast to JSON-friendly format
        forecast_dict = {str(date): value for date, value in forecast.items()}
        return {"status": "success", "forecast": forecast_dict}
    except Exception as e:
        return {"status": "error", "message": str(e)}

if __name__ == "__main__":
    # Read JSON input from stdin
    input_data = json.loads(sys.stdin.read())
    result = predict_inventory(input_data)
    print(json.dumps(result))
