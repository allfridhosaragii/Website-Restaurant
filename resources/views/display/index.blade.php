<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Display</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        .header {
            background-color: #1a1d20;
            padding: 20px;
            font-size: 2rem;
            font-weight: bold;
            border-bottom: 2px solid #333;
            text-align: center;
        }
        .display-column {
            height: calc(100vh - 85px);
            padding: 20px;
        }
        .column-title {
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 4px solid #333;
        }
        .ready-title { color: #198754; border-color: #198754; }
        .served-title { color: #6c757d; border-color: #6c757d; }
        
        .order-card {
            background-color: #1a1d20;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            font-size: 4rem;
            font-weight: bold;
            text-align: center;
            border-left: 10px solid #333;
            transition: all 0.5s ease;
        }
        .order-ready {
            border-left-color: #198754;
            color: #198754;
            animation: pulse 2s infinite;
        }
        .order-served {
            border-left-color: #6c757d;
            color: #6c757d;
            opacity: 0.7;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
            50% { transform: scale(1.02); box-shadow: 0 0 0 20px rgba(25, 135, 84, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }
        
        /* Entrance animation */
        .slide-in {
            animation: slideInRight 0.5s ease-out;
        }
        @keyframes slideInRight {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="orderDisplay()">
    
    <div class="header">
        Pesanan Siap Diambil / <span style="color: #198754;">Ready to Collect</span>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Ready Column -->
            <div class="col-md-6 display-column border-end border-secondary">
                <div class="column-title ready-title">SIAP DIAMBIL</div>
                <div class="d-flex flex-column gap-3">
                    <template x-for="order in readyOrders" :key="order.id">
                        <div class="order-card order-ready slide-in">
                            <div x-text="order.order_number"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Served Column -->
            <div class="col-md-6 display-column">
                <div class="column-title served-title">SUDAH DIAMBIL</div>
                <div class="d-flex flex-column gap-3">
                    <template x-for="order in servedOrders" :key="order.id">
                        <div class="order-card order-served slide-in">
                            <div x-text="order.order_number"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function orderDisplay() {
            return {
                readyOrders: [],
                servedOrders: [],
                announcedOrders: new Set(),
                
                init() {
                    this.fetchData();
                    setInterval(() => this.fetchData(), 5000); // Poll every 5 seconds
                },
                
                async fetchData() {
                    try {
                        const response = await fetch('/display/data');
                        const data = await response.json();
                        
                        this.readyOrders = data.ready;
                        this.servedOrders = data.served;
                        
                        // Check for new ready orders to announce
                        this.readyOrders.forEach(order => {
                            if (!this.announcedOrders.has(order.id)) {
                                this.announceOrder(order.order_number);
                                this.announcedOrders.add(order.id);
                            }
                        });
                        
                    } catch (error) {
                        console.error('Error fetching display data:', error);
                    }
                },
                
                announceOrder(orderNumber) {
                    if ('speechSynthesis' in window) {
                        // Extract digits to read them clearly (e.g., "Satu Dua Tiga")
                        // Wait, just spelling the string might be enough, but numbers read better if space-separated sometimes.
                        const numStr = orderNumber.toString().split('').join(' ');
                        const text = `Pesanan nomor, ${numStr}, siap diambil.`;
                        const msg = new SpeechSynthesisUtterance(text);
                        msg.lang = 'id-ID';
                        msg.rate = 0.9;
                        window.speechSynthesis.speak(msg);
                    }
                }
            }
        }
    </script>
</body>
</html>
