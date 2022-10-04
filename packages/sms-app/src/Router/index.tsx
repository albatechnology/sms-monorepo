import { createBottomTabNavigator } from "@react-navigation/bottom-tabs"
import {
  NavigationContainer,
  NavigationContainerRef,
} from "@react-navigation/native"
import {
  createStackNavigator,
  CardStyleInterpolators,
} from "@react-navigation/stack"
import { StackNavigationOptions } from "@react-navigation/stack/lib/typescript/src/types"
import React from "react"
import { Platform, Pressable } from "react-native"
import { Div, Icon, Image } from "react-native-magnus"

import ActivityDetailScreen from "containers/ActivityDetail"
import ActivityListScreen from "containers/ActivityList"
import ActivityList from "containers/ActivityList"
import AddActivityScreen from "containers/AddActivity"
import AddAddressScreen from "containers/AddAddress"
import AddChatScreen from "containers/AddChat"
import AddCustomerScreen from "containers/AddCustomer"
import AddLeadScreen from "containers/AddLead"
import AddLeadWithCustomerScreen from "containers/AddLeadWithCustomer"
import CafeScreen from "containers/Cafe"
import CartScreen from "containers/Cart"
import ChatScreen from "containers/Chat"
import ChatDetailScreen from "containers/ChatDetail"
import CheckoutScreen from "containers/Checkout"
import NewProductScreen from "containers/Checkout/NewProductScreen"
import CustomerScreen from "containers/Costumer"
import CustomDetailScreen from "containers/CustomerDetail"
import DashboardScreen from "containers/Dashboard"
import InteriorDesign from "containers/Dashboard/InteriorDesign"
import ActivityTotal from "containers/Dashboard/ReportScreen/ActivityTotal"
import BrandDetail from "containers/Dashboard/ReportScreen/BrandDetail"
import EstimatedScreen from "containers/Dashboard/ReportScreen/EstimatedScreen"
import FilterScreen from "containers/Dashboard/ReportScreen/FilterScreen"
import InteriorActivity from "containers/Dashboard/ReportScreen/InteriorActivity"
import InteriorDesignDetail from "containers/Dashboard/ReportScreen/InteriorDesignDetail"
import InteriorDesignScreen from "containers/Dashboard/ReportScreen/InteriorDesignScreen"
import InvoiceScreen from "containers/Dashboard/ReportScreen/InvoiceScreen"
import PipeLineScreen from "containers/Dashboard/ReportScreen/PipeLineScreen"
import AllScreenMap from "containers/Dashboard/ReportScreen/PipiLine/AllScreenMap"
import BumScreen from "containers/Dashboard/ReportScreen/PipiLine/BumScreen"
import SingleChannelList from "containers/Dashboard/ReportScreen/PipiLine/SingleChannelList"
import SingleList from "containers/Dashboard/ReportScreen/PipiLine/SingleList"
import SingleSalesList from "containers/Dashboard/ReportScreen/PipiLine/SingleSalesList"
import TotalChannelScreen from "containers/Dashboard/ReportScreen/PipiLine/TotalChannelScreen"
import TotalEstimated from "containers/Dashboard/ReportScreen/PipiLine/TotalEstimated"
import TotalEstimatedDetail from "containers/Dashboard/ReportScreen/PipiLine/TotalEstimatedDetail"
import TotalHotScreen from "containers/Dashboard/ReportScreen/PipiLine/TotalHotScreen"
import TotalLeadsScreen from "containers/Dashboard/ReportScreen/PipiLine/TotalLeadsScreen"
import TotalNoOfLeads from "containers/Dashboard/ReportScreen/PipiLine/TotalNoOfLeadScreen"
import TotalStatusScreen from "containers/Dashboard/ReportScreen/PipiLine/TotalStatusScreen"
import QuotationScreen from "containers/Dashboard/ReportScreen/QuotationScreen"
import ReportBrandsScreen from "containers/Dashboard/ReportScreen/ReportBrandsScreen"
import ReportCardScreen from "containers/Dashboard/ReportScreen/ReportCardScreen"
import ReportCompareScreen from "containers/Dashboard/ReportScreen/ReportCompareScreen"
import ReportPipeLine from "containers/Dashboard/ReportScreen/ReportPipeLine"
import RevenueSales from "containers/Dashboard/ReportScreen/RevenueSales"
import RevenueScreen from "containers/Dashboard/ReportScreen/RevenueScreen"
import RevenueStoreLeader from "containers/Dashboard/ReportScreen/RevenueStoreLeader"
import SettlementScreen from "containers/Dashboard/ReportScreen/SettlementScreen."
import TopSales from "containers/Dashboard/ReportScreen/TopSales"
import TopSalesDetail from "containers/Dashboard/ReportScreen/TopSalesDetail"
import DiscountApprovalScreen from "containers/DiscountApproval"
import EditActivityScreen from "containers/EditActivity"
import EditAddressScreen from "containers/EditAddress"
import EditCustomerScreen from "containers/EditCustomer"
import EditLeadScreen from "containers/EditLead"
import LoginScreen from "containers/Login"
import NotificationScreen from "containers/Notification"
import OrderPaymentInfoScreen from "containers/OrderPaymentInfo"
import OrderPaymentProofScreen from "containers/OrderPaymentProof"
import PaymentScreen from "containers/Payment"
import PaymentPayCategorySelectionScreen from "containers/PaymentPayCategorySelection"
import PaymentPayConfirmScreen from "containers/PaymentPayConfirm"
import PaymentPayTypeSelectionScreen from "containers/PaymentPayTypeSelection"
import ProductScreen from "containers/Product"
import ProductByBrandScreen from "containers/ProductByBrand"
import ProductDetailScreen from "containers/ProductDetail"
import ProductSearchScreen from "containers/ProductSearch"
import ProductUnitSearchScreen from "containers/ProductUnitSearch"
import PromoScreen from "containers/Promo"
import PromoCategory from "containers/PromoCategory"
import PromoDetailScreen from "containers/PromoDetail"
import ReportDrillDownScreen from "containers/ReportDrillDown"
import SalesActivityScreen from "containers/SalesActivity"
import StockScreen from "containers/Stock"
import StockDetail from "containers/StockDetail"
import StockSelectChannelScreen from "containers/StockSelectChannel"
import TableRevenue from "containers/TableRevenue"
import TargetScreen from "containers/Target"
import TargetDetail from "containers/Target/TargetDetail"
import EstimatedInside from "containers/Target/componentsInside/Estimated"
import FollowTarget from "containers/Target/componentsInside/FollowUp"
import InteriorDesignInside from "containers/Target/componentsInside/InteriorDesign"
import InteriorDesignDetails from "containers/Target/componentsInside/InteriorDesign/InteriorDesign"
import QuotationInside from "containers/Target/componentsInside/Quotation"
import SalesNewLeads from "containers/Target/componentsInside/SalesNewLeads"
import SettlementInside from "containers/Target/componentsInside/Settlement"
import UserSelectChannel from "containers/UserSelectChannel"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import s, { COLOR_PRIMARY, COLOR_DISABLED } from "helper/theme"

import { EntryStackParamList } from "./EntryStackParamList"
import { withLoggedInRedirectMiddleware } from "./LoggedInRedirectMiddleware"
import { CustomerStackParamList, MainTabParamList } from "./MainTabParamList"
import { withRequireLoginMiddleware } from "./RequireLoginMiddleware"
import { withRequireSelectChannelMiddleware } from "./RequireSelectChannel"

const EntryStack = createStackNavigator<EntryStackParamList>()
const MainTab = createBottomTabNavigator<MainTabParamList>()

export const Router = React.forwardRef<NavigationContainerRef>((prop, ref) => {
  return (
    <NavigationContainer ref={ref}>
      <EntryStack.Navigator
        screenOptions={{
          headerTitleStyle: {
            fontFamily: "FontBold",
          },
          headerTitleAlign: "center",
          headerTintColor: "#FFF",
          headerStyle: {
            shadowOpacity: 0,
            backgroundColor: COLOR_PRIMARY,
          },
          headerBackTitle: "Back",
        }}
      >
        <EntryStack.Screen name="Login" options={{ headerShown: false }}>
          {withLoggedInRedirectMiddleware(LoginScreen)}
        </EntryStack.Screen>
        <EntryStack.Screen name="Main" options={{ headerShown: false }}>
          {withRequireLoginMiddleware(
            withRequireSelectChannelMiddleware(MainNavigator),
          )}
        </EntryStack.Screen>
        <EntryStack.Screen name="Notification">
          {withRequireLoginMiddleware(
            withRequireSelectChannelMiddleware(NotificationScreen),
          )}
        </EntryStack.Screen>
        <EntryStack.Screen name="Cart">
          {withRequireLoginMiddleware(
            withRequireSelectChannelMiddleware(CartScreen),
          )}
        </EntryStack.Screen>
        <EntryStack.Screen name="Checkout">
          {withRequireLoginMiddleware(
            withRequireSelectChannelMiddleware(CheckoutScreen),
          )}
        </EntryStack.Screen>
        <EntryStack.Screen
          name="AddAddress"
          options={{
            title: "Add Address",
          }}
          component={AddAddressScreen}
        ></EntryStack.Screen>
        <EntryStack.Screen
          name="NewProduct"
          options={{
            title: "Add Product",
          }}
          component={NewProductScreen}
        ></EntryStack.Screen>
        <EntryStack.Screen
          name="UserSelectChannel"
          options={{ title: "Select Channel" }}
        >
          {withRequireLoginMiddleware(UserSelectChannel)}
        </EntryStack.Screen>
        {/* <EntryStack.Screen
          name="DiscountApproval"
          options={{ title: "Discount Approval" }}
        >
          {withRequireLoginMiddleware(DiscountApprovalScreen)}
        </EntryStack.Screen> */}
        <EntryStack.Screen name="ReportDrillDown" options={{ title: "Report" }}>
          {withRequireLoginMiddleware(ReportDrillDownScreen)}
        </EntryStack.Screen>
        <EntryStack.Screen
          name="ActivityList"
          options={{ title: "Activities" }}
        >
          {withRequireLoginMiddleware(ActivityListScreen)}
        </EntryStack.Screen>
        <EntryStack.Screen
          name="TableRevenue"
          options={{ title: "Sales Revenue" }}
        >
          {withRequireLoginMiddleware(TableRevenue)}
        </EntryStack.Screen>
        <EntryStack.Screen
          name="CustomerDetail"
          options={{
            title: "",
          }}
          component={CustomDetailScreen}
        ></EntryStack.Screen>
        <EntryStack.Screen
          name="ActivityDetail"
          options={{
            title: "Activity Detail",
          }}
          component={ActivityDetailScreen}
        ></EntryStack.Screen>
      </EntryStack.Navigator>
    </NavigationContainer>
  )
})

export const MainNavigator = () => {
  return (
    <MainTab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color }) => {
          let iconName
          let colorSelected = focused ? color : "#C4C4C4"
          switch (route.name) {
            case "Dashboard":
              iconName = require("assets/icon_dashboard.png")
              break
            case "Product":
              iconName = require("assets/icon_product.png")
              break
            case "Promo":
              iconName = require("assets/icon_promo.png")
              break
            case "Chat":
              iconName = require("assets/icon_chat.png")
              break
            case "Customer":
              iconName = require("assets/icon_costumer.png")
              break
          }

          return (
            <Image
              source={iconName}
              w={24}
              h={24}
              style={{ tintColor: colorSelected }}
            />
          )
        },
        headerTitleStyle: {
          fontFamily: "FontBold",
        },
        headerBackTitle: "Back",
      })}
      tabBarOptions={{
        activeTintColor: "#17949D",
        inactiveTintColor: COLOR_DISABLED,
        tabStyle: [Platform.OS === "ios" ? s.pY5 : s.mY5],
        labelStyle: { fontFamily: "FontRegular" },
        keyboardHidesTabBar: Platform.OS === "android" ? true : false,
      }}
    >
      <MainTab.Screen
        name="Chat"
        options={{ tabBarLabel: "Dashboard" }}
        component={ChatStackScreen}
      />
      {/* <MainTab.Screen
        name="Dashboard"
        options={{ tabBarLabel: "Dashboard" }}
        component={DashboardStackScreen}
      /> */}

      <MainTab.Screen
        name="Product"
        options={{ tabBarLabel: "Product" }}
        component={ProductStackScreen}
      />

      <MainTab.Screen
        name="Promo"
        options={{ tabBarLabel: "Promo" }}
        component={PromoStackScreen}
      />

      <MainTab.Screen
        name="Customer"
        options={{ tabBarLabel: "Customer" }}
        component={CustomerStackScreen}
      />
    </MainTab.Navigator>
  )
}

const HeaderRight = ({ toNotification, toCart, toDiscountApproval }) => {
  const { data } = useUserLoggedInData()

  return (
    <Div flex={1} row px={20} alignItems="center">
      <Pressable onPress={toNotification}>
        <Icon
          name="notifications-outline"
          color="#FFF"
          fontSize={16}
          fontFamily="Ionicons"
          mr={10}
        />
      </Pressable>
      {data.type === "SALES" ? (
        <Pressable onPress={toCart}>
          <Icon
            name="cart-outline"
            color="#FFF"
            fontSize={16}
            fontFamily="Ionicons"
          />
        </Pressable>
      ) : (
        <Pressable onPress={toDiscountApproval}>
          <Icon
            name="clock-check-outline"
            color="#FFF"
            fontSize={16}
            fontFamily="MaterialCommunityIcons"
          />
        </Pressable>
      )}
    </Div>
  )
}

const renderOptions = (navigation): StackNavigationOptions => ({
  headerTitleStyle: {
    fontFamily: "FontBold",
  },
  headerTintColor: "#FFF",
  headerStyle: {
    shadowOpacity: 0,
    backgroundColor: COLOR_PRIMARY,
  },
  headerTitleAlign: "center",
  headerRight: () => (
    <HeaderRight
      toNotification={() => navigation.navigate("SalesActivity")}
      toCart={() => navigation.navigate("Cart")}
      toDiscountApproval={() => navigation.navigate("DiscountApproval")}
    />
  ),
  cardStyleInterpolator: CardStyleInterpolators.forHorizontalIOS,
  headerBackTitle: "Back",
})

const renderNoHeaders = (navigation): StackNavigationOptions => ({
  headerTitleStyle: {
    fontFamily: "FontBold",
  },
  headerTintColor: "#FFF",
  headerStyle: {
    shadowOpacity: 0,
    backgroundColor: COLOR_PRIMARY,
  },
  headerTitleAlign: "center",
  cardStyleInterpolator: CardStyleInterpolators.forHorizontalIOS,
  headerBackTitle: "Back",
})

const Dashboard = createStackNavigator()
const DashboardStackScreen = () => {
  return (
    <Dashboard.Navigator
      screenOptions={({ navigation }) => renderOptions(navigation)}
    >
      <Dashboard.Screen
        name="Dashboard"
        component={DashboardScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="SalesActivity"
        options={{
          title: "Sales Activity",
        }}
        component={SalesActivityScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="ActivityDetail"
        options={{
          title: "Activity Detail",
        }}
        component={ActivityDetailScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TopSales"
        options={{
          title: "Top Performance",
        }}
        component={TopSales}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TopSalesDetail"
        options={{
          title: "Top Performance",
        }}
        component={TopSalesDetail}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="FilterScreen"
        options={{
          title: "Filter",
        }}
        component={FilterScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="BrandDetail"
        options={{
          title: "Brand Category",
        }}
        component={BrandDetail}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="Revenue"
        options={{
          title: "Sales Revenue",
        }}
        component={RevenueScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="RevenueStoreLeader"
        options={{
          title: "Sales Revenue",
        }}
        component={RevenueStoreLeader}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="RevenueSales"
        options={{
          title: "Sales Revenue",
        }}
        component={RevenueSales}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="SettlementScreen"
        options={{
          title: "Settlement",
        }}
        component={SettlementScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="InteriorDesignScreen"
        options={{
          title: "Interior Design",
        }}
        component={InteriorDesignScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="InteriorDesignDetail"
        options={{
          title: "Interior Design",
        }}
        component={InteriorDesignDetail}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="InteriorActivity"
        options={{
          title: "Interior Design",
        }}
        component={InteriorActivity}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="InvoiceScreen"
        options={{
          title: "Invoice Manual",
        }}
        component={InvoiceScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="DiscountApproval"
        options={{ title: "Discount Approval" }}
      >
        {withRequireLoginMiddleware(DiscountApprovalScreen)}
      </Dashboard.Screen>
      <Dashboard.Screen
        name="ActivityTotal"
        options={{
          title: "Activity Count",
        }}
        component={ActivityTotal}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="BumScreen"
        options={{
          title: "Table",
        }}
        component={BumScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="ReportBrandsScreen"
        options={{
          title: "Report by Brand",
        }}
        component={ReportBrandsScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="EstimatedScreen"
        options={{
          title: "Estimated Report",
        }}
        component={EstimatedScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="QuotationScreen"
        options={{
          title: "Quotation Report",
        }}
        component={QuotationScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="ReportCardScreen"
        options={{
          title: "Report Brands",
        }}
        component={ReportCardScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="ReportCompareScreen"
        options={{
          title: "Report by Brands",
        }}
        component={ReportCompareScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="PipeLineScreen"
        options={{
          title: "PipeLine",
        }}
        component={PipeLineScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="SingleList"
        options={{
          title: "BUM",
        }}
        component={SingleList}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="SingleChannelList"
        options={{
          title: "Channel",
        }}
        component={SingleChannelList}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="SingleSalesList"
        options={{
          title: "Sales",
        }}
        component={SingleSalesList}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TotalNoOfLeads"
        options={{
          title: "Closing Deals",
        }}
        component={TotalNoOfLeads}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TotalChannelScreen"
        options={{
          title: "Channel",
        }}
        component={TotalChannelScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TotalHotScreen"
        options={{
          title: "Hot",
        }}
        component={TotalHotScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TotalLeadsScreen"
        options={{
          title: "Leads",
        }}
        component={TotalLeadsScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="StatusPipeline"
        options={{
          title: "Status",
        }}
        component={TotalStatusScreen}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="ReportPipeLineScreen"
        options={{
          title: "Report",
        }}
        component={ReportPipeLine}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TotalEstimated"
        options={{
          title: "Estimated",
        }}
        component={TotalEstimated}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="TotalEstimatedDetail"
        options={{
          title: "Estimated Detail",
        }}
        component={TotalEstimatedDetail}
      ></Dashboard.Screen>
      <Dashboard.Screen
        name="AllScreenMap"
        options={{
          title: "PipeLine",
        }}
        component={AllScreenMap}
      ></Dashboard.Screen>
    </Dashboard.Navigator>
  )
}

const Product = createStackNavigator()
const ProductStackScreen = () => {
  return (
    <Product.Navigator
      screenOptions={({ navigation }) => renderOptions(navigation)}
    >
      <Product.Screen name="Product" component={ProductScreen}></Product.Screen>
      <Product.Screen name="Cafe" component={CafeScreen}></Product.Screen>
      <Product.Screen
        name="StockSelectChannel"
        options={{
          title: "Select Channel",
        }}
        component={StockSelectChannelScreen}
      />
      <Product.Screen
        name="Stock"
        options={{
          title: "Stock",
        }}
        component={StockScreen}
      />
      <Product.Screen
        name="ProductDetail"
        options={{
          title: "Product Detail",
        }}
        component={ProductDetailScreen}
      />
      <Product.Screen
        name="ProductByBrand"
        options={{
          title: "",
        }}
        component={ProductByBrandScreen}
      />
      <Product.Screen
        name="ProductSearch"
        options={{
          title: "Search Model",
        }}
        component={ProductSearchScreen}
      />
      <Product.Screen
        name="ProductUnitSearch"
        options={{
          title: "Search Product Unit",
        }}
        component={ProductUnitSearchScreen}
      />
      <Product.Screen
        name="StockDetail"
        options={{
          title: "Product Stock",
        }}
        component={StockDetail}
      />
    </Product.Navigator>
  )
}

const Promo = createStackNavigator()
const PromoStackScreen = () => {
  return (
    <Promo.Navigator
      screenOptions={({ navigation }) => renderOptions(navigation)}
    >
      <Promo.Screen name="Promo Category" component={PromoCategory} />
      <Promo.Screen name="Promo" component={PromoScreen} />
      <Promo.Screen
        name="PromoDetail"
        options={{
          title: "Promo Detail",
        }}
        component={PromoDetailScreen}
      />
      <Promo.Screen
        name="ProductDetail"
        options={{
          title: "Product Detail",
        }}
        component={ProductDetailScreen}
      />
    </Promo.Navigator>
  )
}

const Chat = createStackNavigator()
const ChatStackScreen = () => {
  return (
    <Chat.Navigator
      screenOptions={({ navigation }) => renderOptions(navigation)}
    >
      <Chat.Screen name="Dashboard" component={TargetScreen} />
      <Chat.Screen
        name="SalesActivity"
        options={{
          title: "Sales Activity",
        }}
        component={SalesActivityScreen}
      ></Chat.Screen>
      <Chat.Screen
        name="DiscountApproval"
        options={{ title: "Discount Approval" }}
      >
        {withRequireLoginMiddleware(DiscountApprovalScreen)}
      </Chat.Screen>
      <Chat.Screen name="Target" component={TargetDetail} />
      <Chat.Screen
        name="SalesNewLeads"
        options={{
          title: "New Leads",
        }}
        component={SalesNewLeads}
      />
      <Chat.Screen
        name="TopSales"
        options={{
          title: "Top Performance",
        }}
        component={TopSales}
      />
      <Chat.Screen
        name="SettlementInside"
        options={{
          title: "Settlement",
        }}
        component={SettlementInside}
      />
      <Chat.Screen
        name="EstimatedInside"
        options={{
          title: "Brand Detail",
        }}
        component={EstimatedInside}
      />
      <Chat.Screen
        name="TopSalesDetail"
        options={{
          title: "Top Performance",
        }}
        component={TopSalesDetail}
      />

      <Chat.Screen
        name="QuotationInside"
        options={{
          title: "Invoice Detail",
        }}
        component={QuotationInside}
      />
      <Chat.Screen
        name="InteriorDesignInside"
        options={{
          title: "Interior Design",
        }}
        component={InteriorDesignInside}
      />
      <Chat.Screen
        name="InteriorDesignDetails"
        options={{
          title: "Interior Design",
        }}
        component={InteriorDesignDetails}
      />

      <Chat.Screen name="Chat" component={ChatScreen} />
      <Chat.Screen
        name="ChatDetail"
        options={{
          title: "Chat Detail",
        }}
        component={ChatDetailScreen}
      />
      <Chat.Screen
        name="AddChat"
        options={{
          title: "Add Chat",
        }}
        component={AddChatScreen}
      />
      <Chat.Screen
        name="FollowTarget"
        options={{
          title: "Follow up",
        }}
        component={FollowTarget}
      />
    </Chat.Navigator>
  )
}

const Customer = createStackNavigator<CustomerStackParamList>()
const CustomerStackScreen = () => {
  return (
    <Customer.Navigator
      screenOptions={({ navigation }) => renderOptions(navigation)}
    >
      <Customer.Screen
        name="CustomerList"
        options={{
          title: "Customer List",
        }}
        component={CustomerScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="CustomerDetail"
        options={{
          title: "",
        }}
        component={CustomDetailScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="AddLead"
        options={{
          title: "Add Lead",
        }}
        component={AddLeadScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="AddCustomer"
        options={{
          title: "Add Customer",
        }}
        component={AddCustomerScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="AddLeadWithCustomer"
        options={{
          title: "Add Lead",
        }}
        component={AddLeadWithCustomerScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="AddActivity"
        options={{
          title: "Add Activity",
        }}
        component={AddActivityScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="EditCustomer"
        options={{
          title: "Edit Detail",
        }}
        component={EditCustomerScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="AddAddress"
        options={{
          title: "Add Address",
        }}
        component={AddAddressScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="EditAddress"
        options={{
          title: "Edit Address",
        }}
        component={EditAddressScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="ActivityDetail"
        options={{
          title: "Activity Detail",
        }}
        component={ActivityDetailScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="EditActivity"
        options={{
          title: "Edit Activity",
        }}
        component={EditActivityScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="EditLead"
        options={{
          title: "Edit Lead",
        }}
        component={EditLeadScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="Payment"
        component={PaymentScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="PaymentPayCategorySelection"
        options={{ title: "Payment Category" }}
        component={PaymentPayCategorySelectionScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="PaymentPayTypeSelection"
        options={{ title: "Payment Type" }}
        component={PaymentPayTypeSelectionScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="PaymentPayConfirm"
        options={{ title: "Confirm Payment" }}
        component={PaymentPayConfirmScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="OrderPaymentInfo"
        options={{ title: "Payment Info" }}
        component={OrderPaymentInfoScreen}
      ></Customer.Screen>
      <Customer.Screen
        name="OrderPaymentProof"
        options={{ title: "Payment Proof" }}
        component={OrderPaymentProofScreen}
      ></Customer.Screen>
    </Customer.Navigator>
  )
}
